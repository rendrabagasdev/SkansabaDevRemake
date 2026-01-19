# Login Page System - Documentation

## 📋 Overview

Sistem login page untuk CMS Jurusan RPL dengan fitur keamanan lengkap, rate limiting, dan audit logging. Dirancang khusus untuk staff internal dengan UI formal dan institusional.

## 🎨 Design Specifications

-   **Layout**: Centered card pada light neutral background
-   **Primary Color**: #12B4E0 (RGB: 18, 180, 224)
-   **Branding**: Logo RPL dengan teks "CMS Jurusan RPL" dan subtitle "Sekolah Menengah Kejuruan"
-   **Button Label**: "Masuk" (Indonesian)
-   **Target Audience**: Internal staff only (no public registration)

## 🔐 Security Features

### 1. Rate Limiting

**IP-Based Rate Limiting**

-   Maximum 5 attempts per IP address
-   5 minutes (300 seconds) decay time
-   Automatic blocking after limit exceeded

**Email-Based Rate Limiting**

-   Maximum 5 failed attempts per email in 15 minutes
-   Temporary account blocking
-   Prevents brute force attacks

### 2. Login Audit Logging

Setiap percobaan login (sukses/gagal) dicatat dengan detail:

-   Email yang digunakan
-   User ID (jika sukses)
-   Status (success/failed)
-   IP Address
-   User Agent (browser/device info)
-   Failure Reason (jika gagal)
-   Timestamp

## 📁 File Structure

```
app/
├── Livewire/Auth/
│   └── Login.php                   # Livewire component dengan logic login
├── Http/Requests/
│   └── LoginRequest.php            # Form validation (Indonesian messages)
└── Models/
    └── LoginAudit.php              # Model untuk audit logging

database/migrations/
└── 2026_01_15_122851_create_login_audits_table.php

resources/views/
├── livewire/auth/
│   └── login.blade.php             # UI login page
└── components/layouts/
    └── guest.blade.php             # Layout untuk guest pages

routes/
└── web.php                         # Route configuration
```

## 🚀 Usage

### Accessing Login Page

```
URL: https://your-domain.com/login
```

### Authentication Flow

1. User mengisi email dan password
2. Real-time validation saat input (debounced 300ms)
3. Submit form → trigger rate limit check
4. Validasi credentials
5. Audit logging (success/failed)
6. Redirect ke dashboard (jika sukses)

### Rate Limit Behavior

**Scenario 1: IP Rate Limit**

```
Attempt 1-5: Normal login attempts
Attempt 6: "Terlalu banyak percobaan login. Silakan coba lagi dalam X detik."
```

**Scenario 2: Email-Based Block**

```
5 failed attempts in 15 minutes:
"Akun Anda telah diblokir sementara karena terlalu banyak percobaan login yang gagal. Silakan coba lagi nanti."
```

## 🔧 Technical Implementation

### Livewire Component Features

**1. Real-time Validation**

```php
public function updated($propertyName)
{
    $this->validateOnly($propertyName);
}
```

**2. Password Toggle**

```php
public bool $showPassword = false;

public function togglePassword()
{
    $this->showPassword = !$this->showPassword;
}
```

**3. Loading State**

```php
public bool $isLoading = false;

// Set true saat submit
$this->isLoading = true;
```

**4. Rate Limiting Implementation**

```php
// IP-based
$rateLimitKey = 'login:' . request()->ip();
if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
    // Block
}

// Email-based
$failedAttempts = LoginAudit::countRecentFailedAttempts($this->email, 15);
if ($failedAttempts >= 5) {
    // Block
}
```

**5. Authentication**

```php
if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
    RateLimiter::clear($rateLimitKey);
    request()->session()->regenerate();
    $this->auditSuccessfulLogin();
    return $this->redirect('/dashboard', navigate: true);
}
```

### Audit Logging Methods

**Success Audit**

```php
protected function auditSuccessfulLogin(): void
{
    LoginAudit::create([
        'email' => $this->email,
        'user_id' => Auth::id(),
        'status' => 'success',
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'attempted_at' => now()
    ]);
}
```

**Failed Audit**

```php
protected function auditFailedLogin(string $reason): void
{
    LoginAudit::create([
        'email' => $this->email,
        'status' => 'failed',
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'failure_reason' => $reason,
        'attempted_at' => now()
    ]);
}
```

## 🎯 UI Components

### Form Fields

**Email Input**

-   Type: email
-   Validation: required, email format
-   Auto-focus: Yes
-   Real-time validation dengan debounce 300ms

**Password Input**

-   Type: password/text (toggleable)
-   Validation: required, min 6 characters
-   Toggle button: Eye icon untuk show/hide
-   Auto-complete: current-password

### Button States

**Normal State**

```html
<button class="bg-[#12B4E0] hover:bg-[#0e91b8]">Masuk</button>
```

**Loading State**

```html
<button disabled class="opacity-50 cursor-not-allowed">
    <svg class="animate-spin">...</svg>
    Memproses...
</button>
```

### Error Display

Inline validation errors ditampilkan di bawah field:

```html
@error('email')
<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
@enderror
```

## 📊 Database Schema

### login_audits Table

| Column         | Type              | Description                            |
| -------------- | ----------------- | -------------------------------------- |
| id             | bigint            | Primary key                            |
| email          | string            | Email yang digunakan login             |
| user_id        | bigint (nullable) | Foreign key ke users (null jika gagal) |
| status         | enum              | success/failed                         |
| ip_address     | string            | IP address pengguna                    |
| user_agent     | text (nullable)   | Browser/device info                    |
| failure_reason | string (nullable) | Alasan kegagalan                       |
| attempted_at   | timestamp         | Waktu percobaan login                  |
| created_at     | timestamp         | -                                      |
| updated_at     | timestamp         | -                                      |

**Indexes:**

-   email (untuk query cepat)
-   status (untuk filter success/failed)
-   attempted_at (untuk rate limiting)

## 🔍 Querying Audit Logs

### Get Recent Failed Attempts

```php
$failedCount = LoginAudit::countRecentFailedAttempts('user@example.com', 15);
```

### Get All Successful Logins

```php
$successfulLogins = LoginAudit::successful()->get();
```

### Get Failed Logins in Last 24 Hours

```php
$recentFailed = LoginAudit::failed()
    ->recentAttempts('user@example.com', 1440) // 24 hours in minutes
    ->get();
```

### Get Login History for Specific User

```php
$userLogins = LoginAudit::where('user_id', $userId)
    ->orderBy('attempted_at', 'desc')
    ->get();
```

## 🛣️ Routes Configuration

```php
// Guest routes (unauthenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Auth routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// Public routes
Route::get('/', function () {
    return redirect('/login');
})->name('home');
```

## 🎨 Styling Details

### Color Palette

-   **Primary**: #12B4E0 (Blue - brand color)
-   **Primary Hover**: #0e91b8 (Darker blue)
-   **Background**: #F9FAFB (Light gray)
-   **Card**: #FFFFFF (White)
-   **Text Primary**: #111827 (Gray 900)
-   **Text Secondary**: #6B7280 (Gray 600)
-   **Error**: #DC2626 (Red 600)

### Typography

-   **Font Family**: Inter (from Bunny Fonts)
-   **Logo Title**: 2xl, font-bold
-   **Subtitle**: sm, text-gray-600
-   **Labels**: sm, font-medium
-   **Inputs**: base size
-   **Button**: sm, font-semibold

### Spacing & Layout

-   **Card Width**: max-w-md (28rem)
-   **Card Padding**: p-8
-   **Form Gap**: space-y-6
-   **Logo Size**: 64x64px (h-16 w-16)
-   **Icon Size**: 40x40px (h-10 w-10)

## 🧪 Testing

### Manual Testing Checklist

-   [ ] Login dengan credentials valid → sukses
-   [ ] Login dengan email invalid → error "Format email tidak valid"
-   [ ] Login dengan password < 6 karakter → error "Password minimal 6 karakter"
-   [ ] Login dengan credentials salah → error "Email atau password yang Anda masukkan salah"
-   [ ] 5 percobaan gagal → rate limit triggered
-   [ ] Password toggle berfungsi (show/hide)
-   [ ] Loading state muncul saat submit
-   [ ] Real-time validation bekerja (debounced)
-   [ ] Redirect ke dashboard setelah login sukses
-   [ ] Session regeneration setelah login
-   [ ] Audit log tercatat di database
-   [ ] Forgot password link berfungsi (jika diimplementasikan)

### Rate Limiting Testing

```php
// Test IP-based rate limiting
for ($i = 0; $i < 6; $i++) {
    // Attempt login with wrong credentials
    // 6th attempt should be blocked
}

// Test email-based rate limiting
LoginAudit::factory()->count(5)->create([
    'email' => 'test@example.com',
    'status' => 'failed',
    'attempted_at' => now()->subMinutes(5)
]);

// Next login attempt with this email should be blocked
```

## 🚨 Error Messages (Indonesian)

| Validation          | Error Message                                                                                                   |
| ------------------- | --------------------------------------------------------------------------------------------------------------- |
| Email required      | "Email wajib diisi"                                                                                             |
| Email format        | "Format email tidak valid"                                                                                      |
| Password required   | "Password wajib diisi"                                                                                          |
| Password min length | "Password minimal :min karakter"                                                                                |
| Invalid credentials | "Email atau password yang Anda masukkan salah."                                                                 |
| Rate limit (IP)     | "Terlalu banyak percobaan login. Silakan coba lagi dalam {seconds} detik."                                      |
| Rate limit (Email)  | "Akun Anda telah diblokir sementara karena terlalu banyak percobaan login yang gagal. Silakan coba lagi nanti." |

## 📌 Important Notes

1. **No Public Registration**: System hanya untuk internal staff, tidak ada fitur register
2. **Session Security**: Session di-regenerate setelah login untuk mencegah session fixation
3. **CSRF Protection**: Semua form dilindungi Laravel CSRF token
4. **XSS Protection**: Input di-escape otomatis oleh Blade
5. **Password Hashing**: Laravel menggunakan bcrypt secara default
6. **Livewire Navigate**: Menggunakan `navigate: true` untuk SPA-like navigation tanpa page reload

## 🔄 Future Enhancements

-   [ ] Two-factor authentication (2FA)
-   [ ] Remember me functionality
-   [ ] Password reset via email
-   [ ] Login with Google/Microsoft (SSO)
-   [ ] Admin dashboard untuk monitoring login attempts
-   [ ] Email notification untuk suspicious login
-   [ ] IP whitelist untuk admin accounts
-   [ ] Captcha setelah 3 failed attempts

## 📞 Support

Untuk pertanyaan atau issue terkait login system:

1. Check audit logs: `LoginAudit::latest()->get()`
2. Review rate limiter status
3. Verify user credentials di database
4. Check session configuration

---

**Version**: 1.0.0  
**Last Updated**: 2026-01-15  
**Author**: CMS Jurusan RPL Development Team
