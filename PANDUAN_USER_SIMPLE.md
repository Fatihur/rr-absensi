# Panduan Absensi Mobile - Update Terbaru

## 🎉 Apa yang Baru?

Aplikasi absensi sekarang lebih mudah digunakan di HP! Kami telah memperbaiki cara aplikasi meminta izin untuk mengakses lokasi dan kamera Anda.

## 📱 Cara Menggunakan (Pertama Kali)

### Langkah 1: Buka Aplikasi
1. Buka browser di HP (Chrome/Safari)
2. Ketik alamat aplikasi absensi
3. Login dengan username dan password Anda

### Langkah 2: Izinkan Akses Lokasi
Begitu halaman terbuka, akan muncul **pop-up** seperti ini:

```
┌─────────────────────────────────┐
│  [nama-website] ingin mengakses │
│  lokasi Anda                    │
│                                 │
│  [Tolak]          [Izinkan]    │
└─────────────────────────────────┘
```

**Klik "Izinkan"** agar aplikasi bisa tahu posisi Anda.

✅ Jika berhasil, akan muncul:
- Titik hijau di peta menunjukkan lokasi Anda
- Badge hijau bertulisan "Lokasi diizinkan ✓"

❌ Jika Anda klik "Tolak":
- Akan muncul pesan error
- Akan muncul panduan cara mengizinkan
- Klik tombol "Coba Lagi"

### Langkah 3: Check-In
1. Pastikan lokasi sudah terdeteksi (ada titik hijau di peta)
2. Klik tombol **"Check-In Sekarang"**
3. Akan muncul pop-up izin kamera

### Langkah 4: Izinkan Akses Kamera
```
┌─────────────────────────────────┐
│  [nama-website] ingin mengakses │
│  kamera Anda                    │
│                                 │
│  [Tolak]          [Izinkan]    │
└─────────────────────────────────┘
```

**Klik "Izinkan"** agar bisa ambil foto selfie.

✅ Jika berhasil:
- Kamera akan aktif
- Anda bisa lihat wajah Anda di layar
- Badge hijau "Kamera diizinkan ✓"

### Langkah 5: Ambil Foto
1. Posisikan wajah Anda di tengah layar
2. Pastikan wajah terlihat jelas
3. Klik tombol **"Ambil Foto"**
4. Jika sudah OK, klik **"Submit"**
5. Selesai! ✅

## 🆘 Jika Ada Masalah

### Masalah 1: Pop-up Tidak Muncul
**Penyebab:** Browser sudah pernah menolak izin sebelumnya

**Solusi:**
1. Klik ikon **gembok** 🔒 di sebelah kiri address bar
2. Cari tulisan "Lokasi" atau "Location"
3. Ubah dari "Ditolak" menjadi **"Izinkan"**
4. Refresh halaman (tarik ke bawah)

### Masalah 2: Lokasi Tidak Terdeteksi / Muncul Modal "GPS Tidak Aktif"
**Penyebab:** GPS tidak aktif atau izin ditolak

**Solusi:**
1. Jika muncul modal "GPS Tidak Aktif", klik tombol **"Buka Settings"**
2. Atau buka manual:
   - **Android:** Settings → Location → ON
   - **iOS:** Settings → Privacy → Location Services → ON
3. Pastikan mode GPS diset ke **"High accuracy"** (Android)
4. Pastikan browser punya izin lokasi
5. Kembali ke aplikasi dan klik **"Coba Lagi"**
6. Coba di tempat terbuka (outdoor) untuk GPS lebih akurat
7. Tunggu 10-15 detik untuk GPS lock

### Masalah 3: Kamera Tidak Berfungsi
**Penyebab:** Kamera sedang digunakan atau izin ditolak

**Solusi:**
1. Tutup aplikasi lain yang pakai kamera
2. Pastikan browser punya izin kamera
3. Restart browser
4. Coba lagi

### Masalah 4: Muncul Pesan "HTTPS Required"
**Penyebab:** Anda buka aplikasi dengan http:// bukan https://

**Solusi:**
1. Pastikan alamat website dimulai dengan **https://** (ada huruf 's')
2. Harus ada ikon gembok 🔒 di address bar
3. Jika tidak ada, hubungi admin IT

## 📸 Screenshot Panduan

### Android - Chrome
```
1. Klik ikon gembok 🔒
2. Klik "Permissions" atau "Izin"
3. Cari "Location" → Ubah ke "Allow"
4. Cari "Camera" → Ubah ke "Allow"
5. Refresh halaman
```

### iOS - Safari
```
1. Buka Settings → Safari
2. Scroll ke bawah
3. Cari "Camera" → Ubah ke "Allow"
4. Cari "Location" → Ubah ke "Allow"
5. Kembali ke Safari dan refresh
```

## ✅ Checklist Sebelum Absensi

Sebelum melakukan absensi, pastikan:
- [ ] GPS aktif di HP
- [ ] Koneksi internet stabil
- [ ] Browser punya izin lokasi (badge hijau)
- [ ] Browser punya izin kamera (badge hijau)
- [ ] Anda berada dalam radius kantor
- [ ] Wajah terlihat jelas (tidak gelap)

## 🎯 Tips & Trik

### Agar Lokasi Lebih Akurat:
- Aktifkan GPS di HP
- Gunakan di tempat terbuka (outdoor)
- Tunggu beberapa detik sampai GPS lock
- Pastikan tidak ada penghalang (gedung tinggi, basement)

### Agar Foto Lebih Bagus:
- Pastikan cahaya cukup (tidak gelap)
- Posisikan wajah di tengah layar
- Jangan pakai kacamata hitam atau masker
- Pastikan wajah terlihat jelas

### Hemat Baterai:
- Tutup aplikasi lain yang tidak perlu
- Matikan GPS setelah selesai absensi
- Gunakan mode hemat baterai jika perlu

## ❓ FAQ (Pertanyaan Sering Ditanya)

**Q: Apakah aplikasi akan selalu tracking lokasi saya?**
A: Tidak. Aplikasi hanya mengakses lokasi saat Anda buka halaman absensi. Setelah selesai, tidak ada tracking.

**Q: Apakah foto saya disimpan di HP?**
A: Tidak. Foto langsung dikirim ke server dan tidak disimpan di HP Anda.

**Q: Apakah harus izinkan setiap kali buka aplikasi?**
A: Tidak. Setelah Anda klik "Izinkan" sekali, browser akan ingat pilihan Anda. Tidak perlu izinkan lagi di visit berikutnya.

**Q: Bagaimana jika saya di luar radius kantor?**
A: Anda tetap bisa absensi, tapi akan ditandai sebagai "Di luar radius" dan akan direview oleh admin.

**Q: Apakah bisa pakai browser lain?**
A: Ya, bisa pakai Chrome, Safari, atau Firefox. Tapi kami rekomendasikan Chrome untuk pengalaman terbaik.

**Q: Bagaimana jika HP saya tidak punya GPS?**
A: Aplikasi memerlukan GPS untuk verifikasi lokasi. Jika HP tidak punya GPS, hubungi admin untuk solusi alternatif.

## 📞 Butuh Bantuan?

Jika masih mengalami masalah setelah mengikuti panduan ini:

1. **Screenshot** pesan error yang muncul
2. **Catat** browser dan HP yang digunakan
3. **Hubungi** support:
   - Email: [support-email]
   - WhatsApp: [support-wa]
   - Telepon: [support-phone]

Atau hubungi admin HRD/IT di kantor Anda.

---

## 🎊 Selamat Menggunakan!

Dengan update ini, proses absensi jadi lebih mudah dan jelas. Jika ada saran atau feedback, jangan ragu untuk menghubungi kami.

**Terima kasih!** 🙏
