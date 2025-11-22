# Migrasi Check-In ke Livewire (Hybrid)

## Instalasi Livewire

```bash
# 1. Install Livewire
composer require livewire/livewire

# 2. Publish config
php artisan livewire:publish --config

# 3. Pastikan storage link
php artisan storage:link

# 4. Buat folder untuk foto
mkdir storage/app/public/attendance_photos
```

## Perubahan yang Dilakukan

### 1. Livewire Component
**File:** `app/Livewire/AttendanceCheckIn.php`

**Fitur yang dihandle Livewire:**
- ✅ Break start/end logic
- ✅ Status absensi (reactive)
- ✅ Validasi lokasi
- ✅ Upload & proses foto (via base64)
- ✅ Flash messages
- ✅ Business logic check-in/check-out

**Keuntungan:**
- State management otomatis
- Tidak perlu AJAX manual untuk break
- Real-time UI updates
- Cleaner code separation

### 2. View Livewire
**File:** `resources/views/livewire/attendance-check-in.blade.php`

**Fitur yang tetap JavaScript:**
- 🗺️ Leaflet map & geolocation
- 📷 Camera access (getUserMedia)
- 😊 Face detection (face-api.js)
- 🎨 Canvas manipulation

**Komunikasi Livewire ↔ JavaScript:**
```javascript
// JavaScript → Livewire
@this.dispatch('locationUpdated', { latitude, longitude, distance });
@this.dispatch('photoSubmitted', { photoData, action });

// Livewire → JavaScript
window.addEventListener('closeModal', () => { ... });
```

### 3. Route Update
**File:** `routes/web.php`

**Sebelum:**
```php
Route::get('attendance/check-in', [AttendanceController::class, 'showCheckIn']);
Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
Route::post('attendance/break-start', [AttendanceController::class, 'breakStart']);
Route::post('attendance/break-end', [AttendanceController::class, 'breakEnd']);
Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
```

**Sesudah:**
```php
Route::get('attendance/check-in', App\Livewire\AttendanceCheckIn::class);
```

Semua POST routes untuk break & check-in/out sekarang dihandle oleh Livewire component.

## Cara Kerja

### Break Start/End
```blade
<button wire:click="breakStart" wire:loading.attr="disabled">
  <span wire:loading.remove>Mulai Istirahat</span>
  <span wire:loading>Loading...</span>
</button>
```

### Photo Submission
1. JavaScript capture foto dari camera
2. Convert ke base64
3. Dispatch event ke Livewire: `@this.dispatch('photoSubmitted', { photoData, action })`
4. Livewire decode base64 → save file → process attendance
5. Livewire dispatch 'closeModal' → JavaScript close modal & reload

### Location Tracking
1. JavaScript get geolocation
2. Calculate distance
3. Dispatch ke Livewire: `@this.dispatch('locationUpdated', { lat, lng, distance })`
4. Livewire update state untuk validasi

## Testing

1. **Break Start/End:** Klik tombol, lihat loading state, refresh otomatis
2. **Check-In:** Ambil foto → submit → modal close → page reload
3. **Location:** Map tetap interactive, data location sync ke Livewire
4. **Face Detection:** Tetap berjalan real-time di JavaScript

## Catatan

- ⚠️ Pastikan folder `storage/app/public/attendance_photos` exists
- ⚠️ Run `php artisan storage:link` jika belum
- ✅ Backward compatible dengan mobile routes (tidak terpengaruh)
- ✅ Controller lama bisa dihapus jika tidak digunakan mobile routes
