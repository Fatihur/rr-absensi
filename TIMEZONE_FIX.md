# Timezone Fix - Perbedaan Waktu Check-In

## 🐛 Problem

Ketika user check-in di smartphone jam 06:25 (pagi), data yang tersimpan di database adalah 22:25 (malam sebelumnya). Perbedaan 8 jam ini disebabkan oleh timezone issue.

### Root Cause
1. **Server timezone:** UTC (default Laravel)
2. **Indonesia timezone:** WIB (UTC+7) atau WITA (UTC+8)
3. **Database columns:** Menggunakan `time` bukan `datetime`
4. **Perbedaan:** 8 jam = UTC+8 (WITA) atau 7 jam = UTC+7 (WIB)

### Example
```
User check-in: 06:25 WIB (Indonesia)
Server time: 23:25 UTC (previous day)
Saved in DB: 23:25 (without timezone info)
Display: 23:25 (wrong!)
```

## ✅ Solutions Implemented

### 1. Change Application Timezone
**File:** `config/app.php`

**Before:**
```php
'timezone' => 'UTC',
```

**After:**
```php
'timezone' => 'Asia/Jakarta',
```

**Impact:**
- All `Carbon::now()` will use Asia/Jakarta timezone
- All `now()` helper will use Asia/Jakarta timezone
- Timestamps will be stored correctly

### 2. Change Database Columns to DateTime
**File:** `database/migrations/2025_11_24_060434_change_checkin_checkout_to_datetime_in_attendances_table.php`

**Before:**
```php
$table->time('check_in')->nullable();
$table->time('check_out')->nullable();
```

**After:**
```php
$table->dateTime('check_in')->nullable();
$table->dateTime('check_out')->nullable();
```

**Why:**
- `time` only stores HH:MM:SS (no date, no timezone)
- `datetime` stores full timestamp with date
- Laravel can properly handle timezone conversion with datetime

### 3. Model Already Has Correct Casting
**File:** `app/Models/Attendance.php`

```php
protected $casts = [
    'date' => 'date',
    'check_in' => 'datetime',  // ✅ Already correct
    'check_out' => 'datetime', // ✅ Already correct
    'break_start' => 'datetime',
    'break_end' => 'datetime',
];
```

**Good:**
- Model already casts to datetime
- Will work correctly after migration

## 🔧 Deployment Steps

### Step 1: Update Config
```bash
# Already done in config/app.php
# 'timezone' => 'Asia/Jakarta',
```

### Step 2: Run Migration
```bash
php artisan migrate
```

This will:
- Change `check_in` from `time` to `datetime`
- Change `check_out` from `time` to `datetime`
- Preserve existing data

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

### Step 4: Verify
```bash
# Check current timezone
php artisan tinker
> config('app.timezone')
=> "Asia/Jakarta"

# Test Carbon
> \Carbon\Carbon::now()
=> Carbon\Carbon @1700000000 {
     date: 2025-11-24 06:25:00.0 Asia/Jakarta (+07:00)
   }
```

## 📊 Before vs After

### Before Fix
```
User Action: Check-in at 06:25 WIB
Server Process: Carbon::now() = 23:25 UTC (previous day)
Database Save: 23:25 (time column, no timezone)
Display: 23:25 ❌ WRONG!
```

### After Fix
```
User Action: Check-in at 06:25 WIB
Server Process: Carbon::now() = 06:25 Asia/Jakarta
Database Save: 2025-11-24 06:25:00 (datetime column)
Display: 06:25 ✅ CORRECT!
```

## 🌍 Timezone Options

### Indonesia Timezones
```php
'Asia/Jakarta'   // WIB (UTC+7) - Java, Sumatra
'Asia/Makassar'  // WITA (UTC+8) - Bali, Kalimantan, Sulawesi
'Asia/Jayapura'  // WIT (UTC+9) - Papua, Maluku
```

### Current Setting
```php
'timezone' => 'Asia/Jakarta', // WIB (UTC+7)
```

**Note:** Jika perusahaan di Bali/Makassar, ubah ke `Asia/Makassar` (WITA, UTC+8)

## 🧪 Testing

### Test Case 1: Check-In Time
```php
// Test in tinker
php artisan tinker

// Create test attendance
$attendance = new \App\Models\Attendance();
$attendance->employee_id = 1;
$attendance->date = today();
$attendance->check_in = now();
$attendance->save();

// Verify
$attendance->check_in->format('Y-m-d H:i:s');
// Should show current time in Asia/Jakarta timezone
```

### Test Case 2: Display Time
```blade
<!-- In blade template -->
{{ $attendance->check_in->format('H:i') }}
<!-- Should show correct time -->

{{ $attendance->check_in->format('d M Y H:i:s') }}
<!-- Should show: 24 Nov 2025 06:25:00 -->
```

### Test Case 3: Mobile Check-In
```
1. Open app on smartphone
2. Check current time on phone: 06:25
3. Do check-in
4. Verify in database: should be 06:25, not 22:25 or 23:25
5. Verify in app display: should show 06:25
```

## 🔍 Troubleshooting

### Issue 1: Still showing wrong time after migration
**Solution:**
```bash
# Clear all cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize

# Restart server
php artisan serve
```

### Issue 2: Old data still wrong
**Solution:**
Old data with `time` type cannot be fixed automatically. Options:
1. Accept old data as-is (historical)
2. Manual correction with SQL:
```sql
-- Add 7 hours to old data (if was UTC, now WIB)
UPDATE attendances 
SET check_in = DATE_ADD(check_in, INTERVAL 7 HOUR)
WHERE check_in < '2025-11-24'; -- before fix date
```

### Issue 3: Different timezone needed
**Solution:**
```php
// config/app.php
'timezone' => 'Asia/Makassar', // For WITA (UTC+8)
// or
'timezone' => 'Asia/Jayapura',  // For WIT (UTC+9)
```

## 📝 Migration File

### Full Migration Code
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Change check_in and check_out from time to datetime
            $table->dateTime('check_in')->nullable()->change();
            $table->dateTime('check_out')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revert back to time
            $table->time('check_in')->nullable()->change();
            $table->time('check_out')->nullable()->change();
        });
    }
};
```

## 🎯 Verification Checklist

After deployment, verify:
- [ ] Config timezone is `Asia/Jakarta`
- [ ] Migration ran successfully
- [ ] Database columns are `datetime` type
- [ ] Cache cleared
- [ ] Test check-in shows correct time
- [ ] Old data display (if any) is acceptable
- [ ] Mobile app shows correct time
- [ ] Desktop app shows correct time
- [ ] Reports show correct time

## 📊 Impact Analysis

### Affected Features
- ✅ Check-in time
- ✅ Check-out time
- ✅ Break start/end time (already datetime)
- ✅ Attendance reports
- ✅ Late calculation
- ✅ Work hours calculation
- ✅ Overtime calculation

### Not Affected
- ❌ Date (already correct)
- ❌ Location (lat/lng)
- ❌ Photos
- ❌ Status

## 🚀 Rollback Plan

If issues occur:
```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Revert config
# config/app.php
'timezone' => 'UTC',

# Clear cache
php artisan config:clear
php artisan cache:clear
```

## 📞 Support

### Common Questions

**Q: Apakah data lama akan otomatis terkoreksi?**
A: Tidak. Data lama tetap seperti semula. Hanya data baru yang akan tersimpan dengan benar.

**Q: Apakah perlu update semua data lama?**
A: Opsional. Jika data lama penting, bisa dikoreksi manual dengan SQL. Jika tidak, biarkan saja.

**Q: Apakah timezone bisa berbeda per user?**
A: Tidak di implementasi ini. Semua user menggunakan timezone yang sama (Asia/Jakarta).

**Q: Bagaimana jika perusahaan punya cabang di timezone berbeda?**
A: Pilih timezone kantor pusat. Atau implementasi timezone per branch (advanced).

---

**Version:** 2.1.2
**Release Date:** 2025-11-24
**Status:** ✅ Ready for Deployment
**Priority:** 🔴 HIGH (Data Integrity Issue)
