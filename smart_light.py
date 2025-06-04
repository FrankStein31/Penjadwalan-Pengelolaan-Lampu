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
        # KOREKSI FINAL UNTUK AttributeError: Menggunakan _name_
        log_string += f" - {type(error)._name_}: {str(error)}"
    print(log_string)

# --- Fungsi Waktu ---
def sync_ntp():
    global last_ntp_sync # Deklarasi global karena kita memodifikasi last_ntp_sync
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
    # RTC.datetime() mengembalikan (tahun, bulan, hari, hari_dalam_minggu, jam, menit, detik, subdetik)
    year, month, day, weekday, hours, minutes, seconds, _ = rtc.datetime()
    return f"{year:04d}-{month:02d}-{day:02d} {hours:02d}:{minutes:02d}:{seconds:02d}"

def get_current_time_hhmm():
    # Mengembalikan waktu dalam format HH:MM, berguna untuk cek jadwal
    _, _, _, _, hours, minutes, _, _ = rtc.datetime()
    return f"{hours:02d}:{minutes:02d}"

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
    global light_on, retry_count # Deklarasi global karena kita mungkin memodifikasi light_on dan retry_count
    response = None # Inisialisasi response ke None
    try:
        log_message("INFO", f"Mengambil status lampu {LAMPU_ID} dari {API_BASE_URL}/lampu/{LAMPU_ID}")
        response = urequests.get(f"{API_BASE_URL}/lampu/{LAMPU_ID}")
        data = response.json()
        
        # Reset counter percobaan ulang saat API berhasil
        retry_count = 0 
        
        # Ambil status dari database dengan .get() untuk keamanan dan konversi ke boolean
        db_status = bool(data.get('status'))
        db_otomatis = bool(data.get('otomatis'))
        db_jadwal = bool(data.get('jadwal'))
        db_intensitas = data.get('intensitas', 0) # Default ke 0 jika tidak ada
        
        log_message("INFO", f"Status dari DB: Jadwal={db_jadwal}, Otomatis={db_otomatis}, Lampu={'ON' if db_status else 'OFF'}")
        
        # Fungsi ini memperbarui relay hanya jika dalam mode Manual atau Jadwal.
        # Jika dalam mode Otomatis, logika sensor di main loop yang akan mengambil alih.
        if db_jadwal or (not db_otomatis and not db_jadwal): # Jika mode jadwal aktif ATAU mode manual (jadwal & otomatis mati)
            if db_status != light_on:
                relay.value(1 if db_status else 0)
                light_on = db_status
                log_message("INFO", f"Relay diupdate dari DB (mode Manual/Jadwal): {'ON' if light_on else 'OFF'}")
        
        return {
            'status': db_status,
            'otomatis': db_otomatis,
            'jadwal': db_jadwal,
            'intensitas': db_intensitas
        }
    except (OSError, ValueError) as e: # Tangkap error jaringan dan parsing JSON
        retry_count += 1
        log_message("ERROR", f"Kesalahan API saat mengambil status (Percobaan {retry_count}/{MAX_RETRIES})", e)
        if retry_count >= MAX_RETRIES:
            log_message("WARNING", "Melebihi batas maksimum percobaan API, menunggu 30 detik...")
            time.sleep(30)
            retry_count = 0 # Reset setelah jeda panjang
        return None
    except Exception as e: # Tangkap error lain yang tidak terduga
        log_message("ERROR", "Kesalahan tidak terduga saat mengambil status lampu", e)
        return None
    finally:
        if response:
            response.close() # Pastikan response selalu ditutup

def check_schedule():
    global light_on # Deklarasi global karena kita mungkin memodifikasi light_on
    response = None # Inisialisasi response ke None
    try:
        current_schedule_time_hhmm = get_current_time_hhmm() # Dapatkan string HH:MM
        log_message("INFO", f"Mengecek jadwal lampu dari server pada {current_schedule_time_hhmm}...")
        
        # Tambahkan current_time ke query string agar Laravel dapat memproses jadwal spesifik
        response = urequests.get(f"{API_BASE_URL}/jadwal/execute?current_time={current_schedule_time_hhmm}")
        data = response.json()
        
        if data.get('success'):
            log_message("INFO", "Jadwal berhasil dieksekusi di server. Memperbarui status lampu...")
            # Setelah jadwal dieksekusi oleh server, status lampu di DB mungkin sudah berubah.
            # check_lampu_status akan mengambil status terbaru dan memperbarui light_on dan relay.value().
            lampu_status_after_schedule = check_lampu_status() 
            if lampu_status_after_schedule:
                new_status = lampu_status_after_schedule['status']
                if new_status != light_on: # Hanya log perubahan status jika memang ada perubahan
                    log_message("INFO", f"Mode Jadwal - Status Lampu diubah menjadi: {'Menyala' if new_status else 'Mati'}")
                else:
                    log_message("INFO", f"Mode Jadwal - Status Lampu tetap: {'Menyala' if light_on else 'Mati'}")
            return True
        else:
            log_message("INFO", f"Tidak ada perubahan jadwal yang perlu dieksekusi untuk waktu {current_schedule_time_hhmm}.")
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
    response = None # Inisialisasi response ke None
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

## *Program Utama*

# Koneksi ke WiFi dan sinkronisasi waktu, coba terus-menerus dengan jeda jika gagal
log_message("INFO", "Mencoba koneksi WiFi dan sinkronisasi NTP awal...")
while True:
    if connect_wifi():
        if sync_ntp(): # Hanya sinkronkan NTP jika WiFi berhasil terhubung
            break # Keluar dari loop jika WiFi dan NTP berhasil
    # Jika koneksi atau sinkronisasi gagal, connect_wifi() sudah mencatat error.
    # Cukup jeda dan coba lagi di sini.
    log_message("FATAL", "Tidak dapat melanjutkan tanpa koneksi WiFi dan waktu yang benar. Mencoba lagi...")
    time.sleep(10) # Tunggu 10 detik sebelum mencoba lagi

log_message("INFO", "Memulai kontrol lampu pintar multi-mode...")

while True:
    try:
        current_time_seconds = time.time() # Gunakan nama variabel yang berbeda untuk kejelasan
        
        # Sinkronkan ulang NTP setiap interval
        if current_time_seconds - last_ntp_sync >= NTP_SYNC_INTERVAL:
            sync_ntp()
        
        # Cek status keseluruhan dan mode dari database secara berkala
        if current_time_seconds - last_check >= CHECK_INTERVAL:
            current_lampu_status = check_lampu_status() # Simpan status dalam variabel lokal
            last_check = current_time_seconds
            
            if not current_lampu_status: # Jika gagal mengambil status dari DB, lewati logika untuk iterasi ini
                log_message("WARNING", "Tidak dapat mengambil status lampu dari DB. Melewatkan logika kontrol untuk iterasi ini.")
                time.sleep(1) 
                continue # Lanjutkan ke iterasi loop utama berikutnya
            
            # --- Logika Prioritas Mode ---
            
            # Mode 1: Jadwal (Prioritas Tertinggi)
            if current_lampu_status['jadwal']:
                log_message("INFO", "Mode Jadwal Aktif")
                
                # Cek jadwal lebih sering saat mode jadwal aktif
                if current_time_seconds - last_schedule_check >= SCHEDULE_CHECK_INTERVAL:
                    log_message("INFO", "Mengecek jadwal...")
                    check_schedule() # Fungsi ini akan memanggil check_lampu_status lagi untuk memperbarui relay/light_on
                    last_schedule_check = current_time_seconds
                
                # light_on dan relay sudah diperbarui oleh check_schedule() (yang memanggil check_lampu_status).
                # Jadi, cukup log status saat ini dan lanjutkan.
                log_message("INFO", f"Mode Jadwal - Status Lampu: {'ON' if light_on else 'OFF'}")
                time.sleep(0.5) # Jeda singkat untuk mencegah looping terlalu cepat
                continue # Lewati mode lain dan lanjutkan ke iterasi loop utama berikutnya
            
            # Mode 2: Otomatis (Sensor) - jika jadwal TIDAK aktif
            elif current_lampu_status['otomatis']:
                log_message("INFO", "Mode Otomatis Aktif")
                distance = get_distance()
                
                if distance != -1: # Hanya lanjutkan jika pembacaan sensor valid
                    log_message("INFO", f"Jarak: {distance:.2f} cm")
                    
                    # Logika untuk menyalakan lampu via sensor
                    if distance < 10 and not light_on:
                        log_message("INFO", "Objek terdeteksi < 10 cm. Menyalakan lampu...")
                        relay.value(1)
                        light_on = True
                        update_lampu_status(True, 100) # Perbarui status ke DB
                            
                    # Logika untuk mematikan lampu via sensor
                    elif distance >= 10 and light_on:
                        log_message("INFO", "Objek tidak terdeteksi atau > 10 cm. Mematikan lampu...")
                        relay.value(0)
                        light_on = False
                        update_lampu_status(False, 0) # Perbarui status ke DB
                else:
                    log_message("WARNING", "Gagal membaca sensor ultrasonik dalam mode otomatis.")
                
                time.sleep(0.5) # Jeda singkat
                continue # Lewati mode manual dan lanjutkan ke iterasi loop utama berikutnya
            
            # Mode 3: Manual (Default) - Jika Jadwal dan Otomatis TIDAK aktif
            else: # Blok ini dieksekusi jika current_lampu_status['jadwal'] False DAN current_lampu_status['otomatis'] False
                log_message("INFO", "Mode Manual Aktif")
                # Status relay dan variabel light_on sudah diperbarui oleh check_lampu_status()
                # di awal blok if current_time_seconds - last_check,
                # berdasarkan db_status. Tidak ada kontrol relay langsung yang diperlukan di sini.
            
        time.sleep(0.1) # Jeda singkat di luar blok if last_check untuk menjaga loop tetap responsif
        
    except Exception as e:
        log_message("FATAL", "Error tidak terduga dalam loop utama", e)
        time.sleep(5) # Jeda 5 detik sebelum mencoba lagi setelah error fatal