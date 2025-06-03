from machine import Pin, time_pulse_us, RTC
import time
import network
import urequests
import json
import ntptime # Pastikan ntptime library terinstal di ESP32 Anda

# ======== KONFIGURASI WIFI =========
WIFI_SSID = 'Piskip Dalam'
WIFI_PASSWORD = 'piskip5758'

# ======== KONFIGURASI API =========
API_BASE_URL = "http://192.168.10.76:8000/api"  # URL server Laravel
LAMPU_ID = 3  # Sesuaikan dengan ID lampu di database

# ======== KONFIGURASI SENSOR ULTRASONIK =========
TRIG_PIN = 5
ECHO_PIN = 4
trigger = Pin(TRIG_PIN, Pin.OUT)
echo = Pin(ECHO_PIN, Pin.IN)

# ======== KONFIGURASI RELAY =========
RELAY_PIN = 25
relay = Pin(RELAY_PIN, Pin.OUT)
relay.value(0) # Pastikan relay mati di awal

# ======== KONFIGURASI RTC =========
rtc = RTC()

# Variabel status global
light_on = False # Status lampu saat ini pada ESP32
last_check = 0
last_schedule_check = 0
last_ntp_sync = 0
CHECK_INTERVAL = 3  # Cek database (status lampu dan mode) setiap 3 detik
SCHEDULE_CHECK_INTERVAL = 5  # Cek jadwal di server setiap 5 detik
NTP_SYNC_INTERVAL = 3600  # Sync waktu setiap 1 jam (3600 detik = 1 jam)

retry_count = 0
MAX_RETRIES = 3 # Maksimum percobaan ulang untuk koneksi API/WiFi sebelum jeda panjang

# Tambahkan timezone offset untuk WIB (+7)
UTC_OFFSET = 7 * 3600  # 7 jam dalam detik

# --- Fungsi Logging Kustom ---
def log_message(level, message, error=None):
    current_log_time = get_formatted_time() # Ambil waktu saat ini untuk log
    log_string = f"[{current_log_time}][{level}] {message}"
    if error:
        # *FIXED HERE:* Use _name_ for exception type
        log_string += f" - {type(error)._name_}: {str(error)}"
    print(log_string)

# --- Fungsi Waktu ---
def sync_ntp():
    global last_ntp_sync
    try:
        log_message("INFO", "Mencoba sinkronisasi waktu dengan NTP server...")
        ntptime.settime()
        # Sesuaikan waktu dengan timezone WIB
        _, _, _, _, hours, minutes, seconds, _ = rtc.datetime()
        current = time.time() + UTC_OFFSET
        (year, month, day, hours, minutes, seconds, weekday, _) = time.localtime(current)
        rtc.datetime((year, month, day, weekday, hours, minutes, seconds, 0))
        
        last_ntp_sync = time.time()
        log_message("INFO", f"Waktu berhasil disinkronkan dengan NTP server: {get_formatted_time()}")
        return True
    except Exception as e:
        log_message("ERROR", "Gagal sinkronisasi waktu NTP", e)
        return False

def get_formatted_time():
    # RTC.datetime() returns (year, month, day, weekday, hours, minutes, seconds, subseconds)
    year, month, day, weekday, hours, minutes, seconds, _ = rtc.datetime()
    return f"{year:04d}-{month:02d}-{day:02d} {hours:02d}:{minutes:02d}:{seconds:02d}"

def get_current_time_hhmm():
    # Returns time in HH:MM format, useful for schedule checks if using exact time
    _, _, _, _, hours, minutes, _, _ = rtc.datetime()
    return f"{hours:02d}:{minutes:02d}"

# --- Fungsi Koneksi WiFi ---
def connect_wifi():
    global retry_count # We're managing retry_count here for Wi-Fi as well
    try:
        wlan = network.WLAN(network.STA_IF)
        wlan.active(True)
        if not wlan.isconnected():
            log_message("INFO", 'Menghubungkan ke WiFi...')
            wlan.connect(WIFI_SSID, WIFI_PASSWORD)
            
            connect_attempts = 0
            while not wlan.isconnected() and connect_attempts < 20: # Increased attempts for Wi-Fi stability
                time.sleep(1)
                connect_attempts += 1
            
            if not wlan.isconnected():
                raise Exception("Gagal terhubung ke WiFi setelah 20 detik")
        
        log_message("INFO", f'Terhubung ke WiFi: {wlan.ifconfig()[0]}')
        retry_count = 0 # Reset API retry counter on successful Wi-Fi connection
        return True
    except Exception as e:
        log_message("ERROR", "Koneksi WiFi gagal", e)
        retry_count += 1 # Use retry_count for Wi-Fi failures too
        if retry_count >= MAX_RETRIES:
            log_message("WARNING", "Melebihi batas maksimum percobaan WiFi, menunggu 30 detik sebelum mencoba lagi...")
            time.sleep(30)
            retry_count = 0 # Reset after long sleep
        return False

# --- Fungsi Sensor Ultrasonik ---
def get_distance():
    try:
        trigger.value(0)
        time.sleep_us(2)
        trigger.value(1)
        time.sleep_us(10)
        trigger.value(0)
        
        duration = time_pulse_us(echo, 1, 30000) # Timeout 30ms for ~5m range
        
        if duration == -1:
            # log_message("WARNING", "Timeout membaca sensor ultrasonik") # Often happens if no object
            return -1 # Just return -1, log a warning if it persists or needs attention
                
        distance_cm = (duration / 2) / 29.1 # Speed of sound: 343m/s or 0.0343 cm/us
        return distance_cm
    except Exception as e:
        log_message("ERROR", "Error membaca sensor ultrasonik", e)
        return -1

# --- Fungsi Komunikasi API ---
def check_lampu_status():
    global light_on, retry_count
    response = None
    try:
        log_message("INFO", f"Mengambil status lampu {LAMPU_ID} dari {API_BASE_URL}/lampu/{LAMPU_ID}")
        response = urequests.get(f"{API_BASE_URL}/lampu/{LAMPU_ID}")
        data = response.json()
        
        # Reset retry counter on successful API call
        retry_count = 0 
        
        # Ambil status dari database dengan .get() untuk keamanan
        db_status = bool(data.get('status'))
        db_otomatis = bool(data.get('otomatis'))
        db_jadwal = bool(data.get('jadwal'))
        db_intensitas = data.get('intensitas', 0)
        
        log_message("INFO", f"Status dari DB: Jadwal={db_jadwal}, Otomatis={db_otomatis}, Lampu={'ON' if db_status else 'OFF'}")
        
        # Update relay berdasarkan mode yang aktif
        if db_jadwal:
            # Mode jadwal aktif, status akan diupdate oleh fungsi check_schedule
            log_message("INFO", "Mode Jadwal aktif, menunggu eksekusi jadwal...")
        elif not db_otomatis:
            # Mode Manual - update relay langsung dari status DB
            if db_status != light_on:
                relay.value(1 if db_status else 0)
                light_on = db_status
                log_message("INFO", f"Mode Manual - Relay diupdate: {'ON' if light_on else 'OFF'}")
        
        return {
            'status': db_status,
            'otomatis': db_otomatis,
            'jadwal': db_jadwal,
            'intensitas': db_intensitas
        }
    except (OSError, ValueError) as e:
        retry_count += 1
        log_message("ERROR", f"Kesalahan API saat mengambil status (Percobaan {retry_count}/{MAX_RETRIES})", e)
        if retry_count >= MAX_RETRIES:
            log_message("WARNING", "Melebihi batas maksimum percobaan API, menunggu 30 detik...")
            time.sleep(30)
            retry_count = 0
        return None
    except Exception as e:
        log_message("ERROR", "Kesalahan tidak terduga saat mengambil status lampu", e)
        return None
    finally:
        if response:
            response.close()

def check_schedule():
    global light_on
    response = None
    try:
        current_schedule_time = get_current_time_hhmm()
        log_message("INFO", f"Mengecek jadwal lampu dari server pada {current_schedule_time}...")
        
        # Tambahkan current_time ke query string
        response = urequests.get(f"{API_BASE_URL}/jadwal/execute?current_time={current_schedule_time}")
        data = response.json()
        
        if data.get('success'):
            log_message("INFO", "Jadwal berhasil dieksekusi di server")
            # Ambil status terbaru setelah jadwal dieksekusi
            lampu_status_after_schedule = check_lampu_status()
            if lampu_status_after_schedule:
                new_status = lampu_status_after_schedule['status']
                if new_status != light_on:
                    relay.value(1 if new_status else 0)
                    light_on = new_status
                    log_message("INFO", f"Mode Jadwal - Status Lampu diubah menjadi: {'Menyala' if new_status else 'Mati'}")
                else:
                    log_message("INFO", f"Mode Jadwal - Status Lampu tetap: {'Menyala' if light_on else 'Mati'}")
            return True
        else:
            log_message("INFO", f"Tidak ada perubahan jadwal untuk waktu {current_schedule_time}")
            return False
    except (OSError, ValueError) as e:
        log_message("ERROR", "Kesalahan API saat mengecek jadwal", e)
        return False
    except Exception as e:
        log_message("ERROR", "Kesalahan tidak terduga saat mengecek jadwal", e)
        return False
    finally:
        if response:
            response.close()

def update_lampu_status(status, intensitas):
    response = None # Initialize response to None
    try:
        data = {
            'status': 1 if status else 0,
            'intensitas': intensitas
        }
        headers = {'Content-Type': 'application/json'}
        log_message("INFO", f"Mengupdate status lampu ke server: {data}")
        response = urequests.post(
            f"{API_BASE_URL}/lampu/{LAMPU_ID}/status",
            json=data,
            headers=headers
        )
        result = response.json()
        if result.get('success'):
            log_message("INFO", "Status lampu berhasil diupdate di server")
        else:
            log_message("WARNING", f"Update status lampu ke server gagal: {result.get('message', 'Unknown error')}")
        return result.get('success', False)
    except (OSError, ValueError) as e:
        log_message("ERROR", "Kesalahan API saat mengupdate status", e)
        return False
    except Exception as e:
        log_message("ERROR", "Kesalahan tidak terduga saat mengupdate status lampu", e)
        return False
    finally:
        if response:
            response.close()

# --- Main Program ---

# Connect to WiFi and sync NTP, retry indefinitely if fails but with pauses
log_message("INFO", "Mencoba koneksi WiFi dan sinkronisasi NTP awal...")
while True:
    if connect_wifi():
        if sync_ntp(): # Only sync NTP if WiFi connected successfully
            break # Exit loop if both WiFi and NTP are successful
    log_message("FATAL", "Tidak dapat melanjutkan tanpa koneksi WiFi dan waktu yang benar. Mencoba lagi...")
    time.sleep(10) # Wait before retrying everything

log_message("INFO", "Memulai kontrol lampu pintar multi-mode...")

while True:
    try:
        current_time = time.time()
        
        # Re-sync NTP every interval
        if current_time - last_ntp_sync >= NTP_SYNC_INTERVAL:
            sync_ntp()
        
        # Check status from database
        if current_time - last_check >= CHECK_INTERVAL:
            current_lampu_status = check_lampu_status()
            last_check = current_time
            
            if not current_lampu_status:
                log_message("WARNING", "Tidak dapat mengambil status lampu dari DB. Melewatkan logika kontrol...")
                time.sleep(1)
                continue
            
            # --- Mode Priority Logic ---
            
            # Mode 1: Jadwal (Prioritas Tertinggi)
            if current_lampu_status['jadwal']:
                log_message("INFO", "Mode Jadwal Aktif")
                # Cek jadwal lebih sering saat mode jadwal aktif
                if current_time - last_schedule_check >= SCHEDULE_CHECK_INTERVAL:
                    log_message("INFO", "Mengecek jadwal...")
                    check_schedule()
                    last_schedule_check = current_time
                continue
            
            # Mode 2: Otomatis (Sensor)
            elif current_lampu_status['otomatis']:
                log_message("INFO", "Mode Otomatis Aktif")
                distance = get_distance()
                
                if distance != -1:
                    log_message("INFO", f"Jarak: {distance:.2f} cm")
                    
                    if distance < 10 and not light_on:
                        log_message("INFO", "Objek terdeteksi < 10 cm. Menyalakan lampu...")
                        relay.value(1)
                        light_on = True
                        update_lampu_status(True, 100)
                    
                    elif distance >= 10 and light_on:
                        log_message("INFO", "Objek tidak terdeteksi atau > 10 cm. Mematikan lampu...")
                        relay.value(0)
                        light_on = False
                        update_lampu_status(False, 0)
                
                time.sleep(0.5)
                continue
            
            # Mode 3: Manual
            else:
                log_message("INFO", "Mode Manual Aktif")
                # Status lampu sudah diupdate di check_lampu_status()
        
        time.sleep(0.1)
        
    except Exception as e:
        log_message("FATAL", "Error dalam loop utama", e)
        time.sleep(5)