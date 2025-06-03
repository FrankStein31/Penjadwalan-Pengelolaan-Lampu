from machine import Pin, time_pulse_us
import time
import network
import urequests
import json

# ======== KONFIGURASI WIFI =========
WIFI_SSID = "nama_wifi_anda"
WIFI_PASSWORD = "password_wifi_anda"

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
relay.value(0)

# Variabel status
light_on = False
last_check = 0
CHECK_INTERVAL = 5  # Cek database setiap 5 detik

def connect_wifi():
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)
    if not wlan.isconnected():
        print('Menghubungkan ke WiFi...')
        wlan.connect(WIFI_SSID, WIFI_PASSWORD)
        while not wlan.isconnected():
            pass
    print('Terhubung ke WiFi:', wlan.ifconfig()[0])

def get_distance():
    trigger.value(0)
    time.sleep_us(2)
    trigger.value(1)
    time.sleep_us(10)
    trigger.value(0)
    
    duration = time_pulse_us(echo, 1, 30000)
    
    if duration == -1:
        return -1
        
    distance_cm = (duration / 2) / 29.1
    return distance_cm

def check_lampu_status():
    try:
        response = urequests.get(f"{API_BASE_URL}/lampu/{LAMPU_ID}")
        data = response.json()
        return {
            'status': data['status'],
            'otomatis': data['otomatis'],
            'intensitas': data['intensitas']
        }
    except:
        print("Gagal mengambil status dari server")
        return None

def update_lampu_status(status, intensitas):
    try:
        data = {
            'status': 1 if status else 0,
            'intensitas': intensitas
        }
        headers = {'Content-Type': 'application/json'}
        response = urequests.post(
            f"{API_BASE_URL}/lampu/{LAMPU_ID}/status",
            json=data,
            headers=headers
        )
        return response.json()['success']
    except:
        print("Gagal mengupdate status ke server")
        return False

# Koneksi ke WiFi
connect_wifi()

print("Memulai kontrol lampu pintar...")
while True:
    current_time = time.time()
    
    # Cek status dari database setiap interval
    if current_time - last_check >= CHECK_INTERVAL:
        lampu_status = check_lampu_status()
        last_check = current_time
        
        if lampu_status:
            # Jika mode manual (otomatis = 0)
            if not lampu_status['otomatis']:
                # Ikuti status dari database
                new_status = lampu_status['status'] == 1
                if new_status != light_on:
                    relay.value(1 if new_status else 0)
                    light_on = new_status
                    print("Mode Manual -", "Lampu Menyala" if light_on else "Lampu Mati")
                continue
    
    # Mode otomatis - gunakan sensor
    if lampu_status and lampu_status['otomatis']:
        distance = get_distance()
        
        if distance != -1:
            print(f"Mode Otomatis - Jarak: {distance:.2f} cm")
            
            if distance < 10 and not light_on:
                print("Objek terdeteksi < 10 cm. Menyalakan lampu...")
                relay.value(1)
                light_on = True
                update_lampu_status(True, 100)
                    
            elif distance >= 10 and light_on:
                print("Objek tidak terdeteksi atau > 10 cm. Mematikan lampu...")
                relay.value(0)
                light_on = False
                update_lampu_status(False, 0)
        else:
            print("Gagal membaca sensor ultrasonik")
    
    time.sleep(0.5) 