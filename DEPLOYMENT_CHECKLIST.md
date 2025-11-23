# Deployment Checklist - Permission Fix

## 🚀 Pre-Deployment

### 1. Code Review
- [ ] Review semua perubahan di `resources/views/mobile/attendance.blade.php`
- [ ] Review perubahan di `resources/views/attendances/check-in.blade.php`
- [ ] Review perubahan di `resources/views/branches/create.blade.php`
- [ ] Pastikan tidak ada syntax error
- [ ] Pastikan tidak ada console.log yang tertinggal

### 2. Local Testing
- [ ] Test di Chrome Desktop (Device Mode)
- [ ] Test di Firefox Desktop (Responsive Mode)
- [ ] Test permission grant flow
- [ ] Test permission deny flow
- [ ] Test modal instruksi muncul
- [ ] Test badge status update
- [ ] Test camera quality
- [ ] Test GPS accuracy

### 3. Real Device Testing
- [ ] Test di Android Chrome
- [ ] Test di Android Firefox
- [ ] Test di iOS Safari
- [ ] Test di iOS Chrome
- [ ] Test dengan GPS aktif
- [ ] Test dengan GPS non-aktif
- [ ] Test dengan permission granted
- [ ] Test dengan permission denied

### 4. HTTPS Verification
- [ ] Pastikan SSL certificate aktif
- [ ] Test akses via https://
- [ ] Pastikan tidak ada mixed content warning
- [ ] Test redirect dari http:// ke https://
- [ ] Verify SSL certificate valid (tidak expired)

## 📦 Deployment Steps

### Step 1: Backup
```bash
# Backup database
php artisan backup:run

# Backup files
cp -r resources/views resources/views.backup
```

### Step 2: Deploy Code
```bash
# Pull latest code
git pull origin main

# Or upload files manually:
# - resources/views/mobile/attendance.blade.php
# - resources/views/attendances/check-in.blade.php
# - resources/views/branches/create.blade.php
```

### Step 3: Clear Cache
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimize
php artisan optimize
```

### Step 4: Verify Deployment
- [ ] Access website via HTTPS
- [ ] Check no 500 errors
- [ ] Check no JavaScript errors in console
- [ ] Test permission flow on mobile

## 🧪 Post-Deployment Testing

### Immediate Tests (5 minutes)
- [ ] Open app on mobile browser
- [ ] Verify pop-up izin lokasi muncul
- [ ] Klik Allow → verify badge hijau
- [ ] Verify lokasi terdeteksi di map
- [ ] Klik Check-In → verify pop-up kamera muncul
- [ ] Klik Allow → verify kamera aktif
- [ ] Ambil foto → verify bisa submit

### Permission Deny Tests (5 minutes)
- [ ] Clear site data
- [ ] Refresh page
- [ ] Klik Deny pada pop-up lokasi
- [ ] Verify badge merah muncul
- [ ] Verify modal instruksi muncul
- [ ] Klik "Coba Lagi" → verify pop-up muncul lagi

### Cross-Browser Tests (10 minutes)
- [ ] Test di Chrome Android
- [ ] Test di Safari iOS
- [ ] Test di Firefox Android
- [ ] Verify semua browser berfungsi normal

### Edge Cases (10 minutes)
- [ ] Test dengan GPS non-aktif
- [ ] Test dengan kamera sedang digunakan app lain
- [ ] Test dengan koneksi internet lambat
- [ ] Test dengan browser versi lama
- [ ] Test dengan incognito mode

## 📊 Monitoring

### Day 1 Monitoring
- [ ] Monitor error logs untuk permission errors
- [ ] Check user feedback/complaints
- [ ] Monitor support tickets
- [ ] Track permission grant rate
- [ ] Check analytics untuk bounce rate

### Week 1 Monitoring
- [ ] Analyze permission grant rate trend
- [ ] Collect user feedback
- [ ] Identify common issues
- [ ] Update FAQ if needed
- [ ] Optimize based on data

## 📢 User Communication

### Before Deployment
- [ ] Announce maintenance window (if needed)
- [ ] Prepare user guide (PERMISSION_GUIDE.md)
- [ ] Prepare FAQ for support team
- [ ] Brief support team tentang perubahan

### After Deployment
- [ ] Send announcement email
- [ ] Share PERMISSION_GUIDE.md
- [ ] Update internal documentation
- [ ] Train support team
- [ ] Monitor support channels

### Communication Template
```
Subject: Update Aplikasi Absensi - Perbaikan Izin Kamera & Lokasi

Halo Tim,

Kami telah melakukan update pada aplikasi absensi untuk meningkatkan 
pengalaman penggunaan di mobile:

✅ Pop-up izin lokasi dan kamera lebih jelas
✅ Instruksi lengkap jika izin ditolak
✅ Status permission real-time
✅ Kualitas foto lebih baik

Jika mengalami masalah:
1. Pastikan izinkan akses lokasi dan kamera
2. Lihat panduan lengkap: [link ke PERMISSION_GUIDE.md]
3. Hubungi support jika masih bermasalah

Terima kasih!
```

## 🔧 Rollback Plan

### If Critical Issue Found
```bash
# Step 1: Restore backup
cp -r resources/views.backup/* resources/views/

# Step 2: Clear cache
php artisan cache:clear
php artisan view:clear

# Step 3: Verify rollback
# Test app berfungsi normal

# Step 4: Investigate issue
# Check error logs
# Identify root cause
# Fix and redeploy
```

### Rollback Triggers
- [ ] 50%+ users report permission issues
- [ ] Critical bug found (app crash)
- [ ] HTTPS certificate issue
- [ ] Performance degradation
- [ ] Security vulnerability

## 📈 Success Metrics

### Target Metrics (Week 1)
- [ ] 90%+ permission grant rate
- [ ] <5% support tickets terkait permission
- [ ] <1% bounce rate increase
- [ ] 0 critical bugs
- [ ] Positive user feedback

### Monitoring Dashboard
```
Permission Grant Rate:
- Location: ___%
- Camera: ___%

Support Tickets:
- Permission issues: ___
- Other issues: ___

User Feedback:
- Positive: ___
- Negative: ___
- Neutral: ___
```

## 🎯 Post-Deployment Tasks

### Immediate (Day 1)
- [ ] Monitor error logs
- [ ] Respond to support tickets
- [ ] Collect initial feedback
- [ ] Fix critical bugs if any

### Short-term (Week 1)
- [ ] Analyze metrics
- [ ] Update documentation based on feedback
- [ ] Optimize based on data
- [ ] Plan improvements

### Long-term (Month 1)
- [ ] Review overall impact
- [ ] Document lessons learned
- [ ] Plan next iteration
- [ ] Share success story

## 📞 Emergency Contacts

### Technical Issues
- Developer: [Your Name/Contact]
- DevOps: [DevOps Contact]
- Server Admin: [Admin Contact]

### Business Issues
- Product Owner: [PO Contact]
- Support Lead: [Support Contact]
- Management: [Manager Contact]

## 📝 Notes

### Known Limitations
- Requires HTTPS (not HTTP)
- Requires modern browser (Chrome 50+, Safari 11+)
- GPS accuracy depends on device and location
- Camera quality depends on device

### Future Improvements
- [ ] Add permission status to admin dashboard
- [ ] Add analytics for permission grant rate
- [ ] Add A/B testing for permission messaging
- [ ] Add fallback for old browsers
- [ ] Add offline support

---

**Deployment Date:** _______________
**Deployed By:** _______________
**Verified By:** _______________
**Status:** ⬜ Pending | ⬜ In Progress | ⬜ Completed | ⬜ Rolled Back

**Sign-off:**
- [ ] Developer
- [ ] QA
- [ ] Product Owner
- [ ] DevOps
