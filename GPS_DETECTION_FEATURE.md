# GPS Detection Feature - Documentation

## 🎯 Overview

Fitur baru yang mendeteksi apakah GPS/Location Services aktif di perangkat user dan menampilkan modal instruksi jika GPS tidak aktif.

## ✨ Features

### 1. Automatic GPS Detection
- Deteksi otomatis saat request lokasi
- Trigger pada error `POSITION_UNAVAILABLE` dan `TIMEOUT`
- Tampilkan modal khusus dengan instruksi lengkap

### 2. GPS Modal
- **Header:** Icon dan judul "GPS Tidak Aktif"
- **Content:** 
  - Icon warning besar
  - Alert box dengan penjelasan
  - Card instruksi untuk Android (hijau)
  - Card instruksi untuk iOS (biru)
  - Tips box untuk hasil terbaik
- **Footer:**
  - Tombol "Buka Settings" (kuning)
  - Tombol "Coba Lagi" (biru)

### 3. Direct Settings Access
- Tombol "Buka Settings" mencoba membuka pengaturan GPS langsung
- Android: `android.settings.LOCATION_SOURCE_SETTINGS`
- iOS: Alert dengan instruksi (iOS tidak allow direct settings)
- Fallback: Alert dengan instruksi manual

## 📱 User Flow

### Scenario 1: GPS Tidak Aktif
```
1. User buka aplikasi
2. App request lokasi
3. GPS tidak aktif → Error POSITION_UNAVAILABLE
4. Modal "GPS Tidak Aktif" muncul otomatis
5. User baca instruksi
6. User klik "Buka Settings"
7. Settings GPS terbuka (Android) atau alert muncul (iOS)
8. User aktifkan GPS
9. User kembali ke app
10. User klik "Coba Lagi"
11. Lokasi terdeteksi ✅
```

### Scenario 2: GPS Timeout
```
1. User buka aplikasi
2. App request lokasi
3. GPS lambat/tidak dapat lock → Error TIMEOUT
4. Modal "GPS Tidak Aktif" muncul
5. User ikuti instruksi
6. User pindah ke tempat terbuka
7. User klik "Coba Lagi"
8. GPS lock berhasil ✅
```

## 🔧 Technical Implementation

### Detection Logic
```javascript
navigator.geolocation.getCurrentPosition(
  successCallback,
  function(error) {
    switch(error.code) {
      case error.POSITION_UNAVAILABLE:
        // GPS tidak aktif
        showGpsModal();
        break;
      case error.TIMEOUT:
        // GPS timeout (kemungkinan tidak aktif atau sinyal lemah)
        showGpsModal();
        break;
    }
  },
  {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0
  }
);
```

### Modal Structure
```html
<!-- GPS Not Active Modal -->
<div class="camera-modal" id="gpsModal">
  <div class="camera-header">
    <h5>GPS Tidak Aktif</h5>
    <button id="closeGpsModal">×</button>
  </div>
  <div class="camera-content">
    <!-- Icon -->
    <!-- Alert -->
    <!-- Android Instructions -->
    <!-- iOS Instructions -->
    <!-- Tips -->
  </div>
  <div class="camera-footer">
    <button id="openGpsSettings">Buka Settings</button>
    <button id="retryGps">Coba Lagi</button>
  </div>
</div>
```

### Settings Access
```javascript
$('#openGpsSettings').on('click', function() {
  var userAgent = navigator.userAgent;
  
  if (/android/i.test(userAgent)) {
    // Try to open Android location settings
    window.location.href = 'android.settings.LOCATION_SOURCE_SETTINGS';
    
    // Fallback alert
    setTimeout(function() {
      alert('Silakan buka Settings → Location dan aktifkan GPS');
    }, 500);
  }
  else if (/iPad|iPhone|iPod/.test(userAgent)) {
    // iOS doesn't allow direct settings access
    alert('Silakan buka Settings → Privacy & Security → Location Services');
  }
  else {
    // Other devices
    alert('Silakan aktifkan GPS di pengaturan perangkat Anda');
  }
});
```

## 📊 Error Codes

### GeolocationPositionError Codes
```javascript
error.PERMISSION_DENIED = 1
// User denied permission
// Action: Show Permission Help Modal

error.POSITION_UNAVAILABLE = 2
// GPS not active or unavailable
// Action: Show GPS Modal ⭐

error.TIMEOUT = 3
// Request timeout (GPS slow/not active)
// Action: Show GPS Modal ⭐
```

## 🎨 UI/UX Design

### Modal Styling
- **Background:** Dark overlay (rgba(0,0,0,0.9))
- **Size:** Responsive, max-width 500px
- **Colors:**
  - Warning: #ffc107 (yellow)
  - Android: #4CAF50 (green)
  - iOS: #007AFF (blue)
  - Info: #17a2b8 (cyan)

### Icons
- **Main Icon:** `fa-map-marked-alt` (64px, yellow)
- **Android Icon:** `fab fa-android`
- **iOS Icon:** `fab fa-apple`
- **Warning Icon:** `fa-exclamation-triangle`
- **Info Icon:** `fa-info-circle`

### Buttons
- **Buka Settings:** Warning button (yellow)
- **Coba Lagi:** Primary button (blue)
- **Close:** X button (top right)

## 📝 Instructions Content

### Android Instructions
```
1. Buka Settings (Pengaturan)
2. Pilih Location (Lokasi)
3. Aktifkan Use location atau toggle ke ON
4. Pilih mode High accuracy untuk hasil terbaik
5. Kembali ke aplikasi dan klik "Coba Lagi"
```

### iOS Instructions
```
1. Buka Settings (Pengaturan)
2. Pilih Privacy & Security
3. Pilih Location Services
4. Aktifkan toggle Location Services ke ON
5. Scroll ke bawah, cari browser Anda (Safari/Chrome)
6. Pilih While Using the App atau Always
7. Kembali ke aplikasi dan klik "Coba Lagi"
```

### Tips
```
Untuk hasil terbaik, gunakan aplikasi di tempat terbuka (outdoor) 
agar GPS lebih cepat mendeteksi lokasi.
```

## 🧪 Testing

### Test Cases

#### TC1: GPS Tidak Aktif
```
Precondition: GPS disabled di HP
Steps:
1. Buka aplikasi
2. Observe modal muncul
3. Verify instruksi tampil
4. Klik "Buka Settings"
5. Verify settings terbuka atau alert muncul
6. Aktifkan GPS
7. Kembali ke app
8. Klik "Coba Lagi"
Expected: Lokasi terdeteksi
```

#### TC2: GPS Timeout
```
Precondition: GPS enabled tapi sinyal lemah (indoor)
Steps:
1. Buka aplikasi di basement/indoor
2. Wait 10 seconds
3. Observe modal muncul
4. Pindah ke outdoor
5. Klik "Coba Lagi"
Expected: Lokasi terdeteksi
```

#### TC3: Close Modal
```
Steps:
1. Trigger GPS modal
2. Klik X button
Expected: Modal tertutup
```

#### TC4: Android Settings
```
Precondition: Android device
Steps:
1. Trigger GPS modal
2. Klik "Buka Settings"
Expected: Location settings terbuka atau alert muncul
```

#### TC5: iOS Settings
```
Precondition: iOS device
Steps:
1. Trigger GPS modal
2. Klik "Buka Settings"
Expected: Alert dengan instruksi muncul
```

## 📈 Metrics

### Success Metrics
- Reduce "GPS not found" errors by 80%
- Reduce support tickets about location by 60%
- Increase location permission grant rate to 95%+
- User can self-resolve GPS issues without support

### Tracking
```javascript
// Track GPS modal shown
analytics.track('GPS_Modal_Shown', {
  error_code: error.code,
  error_message: error.message
});

// Track settings button clicked
analytics.track('GPS_Settings_Clicked', {
  device_type: 'android' | 'ios' | 'other'
});

// Track retry button clicked
analytics.track('GPS_Retry_Clicked');

// Track success after retry
analytics.track('GPS_Success_After_Retry', {
  retry_count: count
});
```

## 🔍 Troubleshooting

### Issue: Modal tidak muncul
**Cause:** Error code bukan POSITION_UNAVAILABLE atau TIMEOUT
**Solution:** Check error code di console, tambahkan handling jika perlu

### Issue: Settings tidak terbuka di Android
**Cause:** Browser security restrictions
**Solution:** Fallback alert sudah ada, user buka manual

### Issue: GPS tetap tidak terdeteksi setelah diaktifkan
**Cause:** GPS butuh waktu untuk lock
**Solution:** Minta user tunggu 10-15 detik atau pindah ke outdoor

## 🚀 Future Improvements

### Planned Features
- [ ] Detect GPS accuracy level
- [ ] Show GPS signal strength indicator
- [ ] Auto-retry after settings opened
- [ ] Video tutorial link in modal
- [ ] Geolocation API permission query
- [ ] Background location tracking option
- [ ] GPS troubleshooting wizard

### Nice to Have
- [ ] Animated GPS icon
- [ ] Progress bar for GPS lock
- [ ] Map preview in modal
- [ ] Share location via link
- [ ] Save last known location

## 📞 Support

### Common User Questions

**Q: Kenapa modal GPS muncul terus?**
A: GPS belum aktif atau sinyal lemah. Pastikan GPS aktif dan coba di outdoor.

**Q: Sudah aktifkan GPS tapi tetap error?**
A: Tunggu 10-15 detik untuk GPS lock. Coba di tempat terbuka.

**Q: Tombol "Buka Settings" tidak berfungsi?**
A: Buka manual: Settings → Location (Android) atau Settings → Privacy → Location Services (iOS)

**Q: GPS aktif tapi lokasi tidak akurat?**
A: Pastikan mode GPS di "High accuracy" (Android) dan coba di outdoor.

---

**Version:** 2.1.0
**Release Date:** 2025-11-23
**Status:** ✅ Production Ready
**Tested On:** Android Chrome, iOS Safari, Android Firefox
