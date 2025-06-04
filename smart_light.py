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

# ======== KONFIGURASI PIN =========
RELAY_PIN = 25
TRIG_PIN = 5
ECHO_PIN = 4

# Inisialisasi PIN
relay = Pin(RELAY_PIN, Pin.OUT)
trigger = Pin(TRIG_PIN, Pin.OUT)
echo = Pin(ECHO_PIN, Pin.IN)
relay.value(0)  # Matikan relay saat startup

# ======== KONFIGURASI WAKTU =========
rtc = RTC()
UTC_OFFSET = 7 * 3600  # WIB: UTC+7

# ======== VARIABEL GLOBAL =========
light_on = False
last_check = 0
last_schedule_check = 0
last_ntp_sync = 0
last_sensor_check = 0
CHECK_INTERVAL = 3
SCHEDULE_CHECK_INTERVAL = 5
NTP_SYNC_INTERVAL = 3600
SENSOR_CHECK_INTERVAL = 0.5
SENSOR_THRESHOLD = 10  # Jarak dalam cm

retry_count = 0
MAX_RETRIES = 3 # Maksimum percobaan ulang untuk koneksi API/WiFi sebelum jeda panjang

# --- Fungsi Logging Kustom ---
def log_message(level, message, error=None):
    try:
        current_time = get_formatted_time()
        log_string = f"[{current_time}][{level}] {message}"
        if error:
            log_string += f" - Error: {str(error)}"
        print(log_string)
    except:
        print(f"[{level}] {message}")

# --- Fungsi Waktu ---
def sync_ntp():
    global last_ntp_sync
    try:
        log_message("INFO", "Mencoba sinkronisasi waktu dengan NTP server...")
        ntptime.settime()
        
        # Sesuaikan waktu dengan timezone WIB
        current_utc_timestamp = time.time()
        local_timestamp = current_utc_timestamp + UTC_OFFSET
        (year, month, day, hours, minutes, seconds, weekday, yearday) = time.localtime(local_timestamp)
        # Set RTC dengan waktu lokal yang sudah disesuaikan
        rtc.datetime((year, month, day, weekday, hours, minutes, seconds, 0))
        
        last_ntp_sync = current_utc_timestamp # Simpan timestamp UTC untuk cek interval
        log_message("INFO", f"Waktu berhasil disinkronkan dengan NTP server: {get_formatted_time()}")
        return True
    except Exception as e:
        log_message("ERROR", "Gagal sinkronisasi waktu NTP", e)
        return False

def get_formatted_time():
    try:
        # RTC.datetime() mengembalikan (tahun, bulan, hari, hari_dalam_minggu, jam, menit, detik, subdetik)
        year, month, day, weekday, hours, minutes, seconds, _ = rtc.datetime()
        return f"{year:04d}-{month:02d}-{day:02d} {hours:02d}:{minutes:02d}:{seconds:02d}"
    except:
        return "Waktu tidak tersedia"

def get_current_time_hhmm():
    try:
        # Mengembalikan waktu dalam format HH:MM, berguna untuk cek jadwal
        _, _, _, _, hours, minutes, _, _ = rtc.datetime()
        return f"{hours:02d}:{minutes:02d}"
    except:
        return "00:00"

# --- Fungsi Koneksi WiFi ---
def connect_wifi():
    try:
        wlan = network.WLAN(network.STA_IF)
        wlan.active(True)
        if not wlan.isconnected():
            log_message("INFO", 'Menghubungkan ke WiFi...')
            wlan.connect(WIFI_SSID, WIFI_PASSWORD)
            
            connect_attempts = 0
            while not wlan.isconnected() and connect_attempts < 20: # Tingkatkan percobaan untuk stabilitas WiFi
                time.sleep(1)
                connect_attempts += 1
            
            if not wlan.isconnected():
                raise Exception("Gagal terhubung ke WiFi setelah 20 detik")
        
        log_message("INFO", f'Terhubung ke WiFi: {wlan.ifconfig()[0]}')
        # retry_count direset oleh loop utama setelah koneksi WiFi berhasil.
        return True
    except Exception as e:
        log_message("ERROR", "Koneksi WiFi gagal", e)
        return False

# --- Fungsi Sensor Ultrasonik ---
def get_distance():
    try:
        trigger.value(0)
        time.sleep_us(2)
        trigger.value(1)
        time.sleep_us(10)
        trigger.value(0)
        
        duration = time_pulse_us(echo, 1, 30000) # Timeout 30ms untuk jangkauan ~5m
        
        if duration == -1:
            # Mengembalikan -1 jika timeout. Logika pemanggilan akan menentukan apakah perlu logging.
            return -1
                
        distance_cm = (duration / 2) / 29.1 # Kecepatan suara: 343m/s atau 0.0343 cm/us
        return distance_cm
    except Exception as e:
        log_message("ERROR", "Error membaca sensor ultrasonik", e)
        return -1

# --- Fungsi Komunikasi API ---
def check_lampu_status():
    global light_on
    response = None
    try:
        log_message("INFO", f"Mengambil status lampu {LAMPU_ID}")
        response = urequests.get(f"{API_BASE_URL}/lampu/{LAMPU_ID}")
        data = response.json()
        
        # Konversi ke boolean untuk memastikan tipe data yang benar
        db_status = bool(data.get('status', 0))
        db_otomatis = bool(data.get('otomatis', 0))
        db_jadwal = bool(data.get('jadwal', 0))
        
        log_message("INFO", f"Status DB: Jadwal={db_jadwal}, Otomatis={db_otomatis}, Status={'ON' if db_status else 'OFF'}")
        
        # Jika mode jadwal aktif, abaikan status dari database
        # Status akan diatur oleh fungsi check_schedule()
        if not db_jadwal and not db_otomatis:
            if db_status != light_on:
                relay.value(1 if db_status else 0)
                light_on = db_status
                log_message("INFO", f"Manual: Lampu {'ON' if light_on else 'OFF'}")
        
        return {
            'status': db_status,
            'otomatis': db_otomatis,
            'jadwal': db_jadwal
        }
    except Exception as e:
        log_message("ERROR", "Gagal mengambil status", e)
        return None
    finally:
        if response:
            try:
                response.close()
            except:
                pass

def check_schedule():
    global light_on
    response = None
    try:
        current_time = get_current_time_hhmm()
        log_message("INFO", f"Cek jadwal pada: {current_time}")
        
        response = urequests.get(f"{API_BASE_URL}/jadwal/execute?current_time={current_time}")
        data = response.json()
        
        if data.get('success'):
            action = data.get('action')
            intensitas = data.get('intensitas', 0)
            
            log_message("INFO", f"Response jadwal: action={action}, intensitas={intensitas}")
            
            # PERBAIKAN: Logika yang lebih jelas untuk menentukan status lampu
            if action == 'ON' and intensitas > 0:
                # Nyalakan lampu jika ada perintah ON dan intensitas > 0
                if not light_on:
                    relay.value(1)
                    light_on = True
                    log_message("INFO", f"Jadwal: Lampu dinyalakan dengan intensitas {intensitas}%")
                else:
                    log_message("INFO", f"Jadwal: Lampu sudah menyala, intensitas {intensitas}%")
                    
            elif action == 'OFF' or (action == 'ON' and intensitas == 0):
                # Matikan lampu jika ada perintah OFF atau intensitas = 0
                if light_on:
                    relay.value(0)
                    light_on = False
                    log_message("INFO", "Jadwal: Lampu dimatikan")
                else:
                    log_message("INFO", "Jadwal: Lampu sudah mati")
                    
            elif action is None:
                # Tidak ada jadwal yang cocok, maintain status saat ini
                log_message("INFO", f"Tidak ada jadwal aktif saat ini ({current_time})")
                
            return True
        else:
            log_message("WARNING", "Response jadwal tidak success")
            return False
            
    except Exception as e:
        log_message("ERROR", "Gagal cek jadwal", e)
        return False
    finally:
        if response:
            try:
                response.close()
            except:
                pass

def update_lampu_status(status, intensitas=None):
    response = None
    try:
        data = {
            'status': 1 if status else 0
        }
        if intensitas is not None:
            data['intensitas'] = intensitas
            
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

def handle_automatic_mode():
    global light_on, last_sensor_check
    current_time = time.time()
    
    # Cek sensor setiap interval
    if current_time - last_sensor_check >= SENSOR_CHECK_INTERVAL:
        distance = get_distance()
        last_sensor_check = current_time
        
        if distance >= 0:  # Jika pembacaan sensor valid
            log_message("INFO", f"Jarak: {distance:.1f} cm")
            
            # Objek terdeteksi dekat
            if distance < SENSOR_THRESHOLD and not light_on:
                relay.value(1)
                light_on = True
                update_lampu_status(True, 100)
                log_message("INFO", "Otomatis: Objek terdeteksi, lampu dinyalakan")
                
            # Objek menjauh
            elif distance >= SENSOR_THRESHOLD and light_on:
                relay.value(0)
                light_on = False
                update_lampu_status(False, 0)
                log_message("INFO", "Otomatis: Objek menjauh, lampu dimatikan")
        else:
            log_message("WARNING", "Pembacaan sensor tidak valid")

# --- Program Utama ---
def main():
    global last_check, last_schedule_check, last_ntp_sync, light_on, last_sensor_check
    
    # Inisialisasi awal
    log_message("INFO", "Memulai sistem...")
    
    # Loop sampai WiFi dan NTP tersedia
    while True:
        if connect_wifi() and sync_ntp():
            break
        time.sleep(10)
    
    log_message("INFO", "Sistem siap")
    
    while True:
        try:
            current_time = time.time()
            
            # Sinkronisasi NTP
            if current_time - last_ntp_sync >= NTP_SYNC_INTERVAL:
                sync_ntp()
            
            # Cek status dari database
            if current_time - last_check >= CHECK_INTERVAL:
                status = check_lampu_status()
                last_check = current_time
                
                if status:
                    # Mode Jadwal (Prioritas Tertinggi)
                    if status['jadwal']:
                        log_message("INFO", "Mode Jadwal Aktif")
                        # Cek jadwal setiap interval
                        if current_time - last_schedule_check >= SCHEDULE_CHECK_INTERVAL:
                            check_schedule()
                            last_schedule_check = current_time
                    
                    # Mode Otomatis
                    elif status['otomatis']:
                        log_message("INFO", "Mode Otomatis Aktif")
                        handle_automatic_mode()
                    
                    # Mode Manual sudah ditangani di check_lampu_status()
            
            time.sleep(0.1)
            
        except Exception as e:
            log_message("ERROR", "Error di loop utama", e)
            time.sleep(5)

# Mulai program
main()