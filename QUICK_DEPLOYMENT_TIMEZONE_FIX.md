# Quick Deployment Guide - Timezone Fix

## 🚨 Critical Issue
Check-in time salah 8 jam (06:25 tersimpan sebagai 22:25)

## ⚡ Quick Fix (5 menit)

### Step 1: Pull Latest Code
```bash
git pull origin main
```

### Step 2: Run Migration
```bash
php artisan migrate
```

Output yang diharapkan:
```
Migrating: 2025_11_24_060434_change_checkin_checkout_to_datetime_in_attendances_table
Migrated:  2025_11_24_060434_change_checkin_checkout_to_datetime_in_attendances_table (XX.XXms)
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

### Step 4: Restart Server
```bash
# If using php artisan serve
Ctrl+C
php artisan serve

# If using Apache/Nginx
sudo systemctl restart apache2
# or
sudo systemctl restart nginx
```

### Step 5: Test
1. Buka aplikasi di smartphone
2. Cek jam di HP: misal 06:25
3. Lakukan check-in
4. Verify di database atau tampilan: harus 06:25, bukan 22:25

## ✅ Verification

### Check Config
```bash
php artisan tinker
> config('app.timezone')
=> "Asia/Jakarta"  # ✅ Correct
```

### Check Database
```sql
DESCRIBE attendances;
-- check_in should be datetime, not time
-- check_out should be datetime, not time
```

### Check Time
```bash
php artisan tinker
> \Carbon\Carbon::now()
=> Carbon\Carbon @... {
     date: 2025-11-24 06:25:00.0 Asia/Jakarta (+07:00)
   }
```

## 🔄 Rollback (if needed)

```bash
php artisan migrate:rollback --step=1
```

## 📞 Support

Jika ada masalah:
1. Check error logs: `storage/logs/laravel.log`
2. Verify database connection
3. Check timezone config: `config/app.php`
4. Contact developer

## 📋 Changes Summary

### Files Modified
1. `config/app.php`
   - `'timezone' => 'UTC'` → `'timezone' => 'Asia/Jakarta'`

2. `database/migrations/2025_11_24_060434_change_checkin_checkout_to_datetime_in_attendances_table.php`
   - New migration file
   - Changes `check_in` and `check_out` from `time` to `datetime`

### No Code Changes Required
- Controllers already use `Carbon::now()` ✅
- Models already cast to `datetime` ✅
- Views already format correctly ✅

## ⏱️ Downtime
- **Expected:** 0 minutes (no downtime)
- **Migration:** < 1 second
- **Cache clear:** < 5 seconds

## 🎯 Priority
**🔴 CRITICAL** - Deploy ASAP

Data integrity issue affecting all check-in/check-out times.

---

**Prepared by:** Development Team
**Date:** 2025-11-24
**Estimated Time:** 5 minutes
