# SDLC MVP Website Presensi Laravel 10
## Sistem Presensi Sederhana dengan Face Recognition

---

## 1. MVP Overview

### 1.1 Konsep MVP
**Minimum Viable Product (MVP)** adalah versi paling sederhana dari sistem presensi yang dapat berfungsi dan memberikan value kepada user. Fokus pada fitur inti tanpa kompleksitas berlebihan.

### 1.2 Tujuan MVP
- ✅ Menggantikan presensi manual dengan digital
- ✅ Verifikasi identitas menggunakan face recognition
- ✅ Validasi lokasi presensi dengan GPS
- ✅ Dashboard sederhana untuk monitoring
- ✅ Laporan basic kehadiran

### 1.3 Prinsip Pengembangan
- **Keep It Simple**: Fokus pada functionality, bukan fancy features
- **Beginner Friendly**: Code yang mudah dipahami dan di-maintain
- **Functional First**: Yang penting jalan dulu, optimize kemudian
- **One Step at a Time**: Build incrementally

---

## 2. Kebutuhan Minimal

### 2.1 Functional Requirements (Yang Wajib Ada)

#### **Authentication** 
- Login untuk Admin dan User
- Logout
- Simple session management

#### **Presensi Core**
- Clock In dengan foto wajah
- Clock Out dengan foto wajah
- Validasi lokasi GPS
- Simpan record ke database

#### **Dashboard Basic**
- List presensi hari ini
- Status kehadiran karyawan
- Total jam kerja

#### **Laporan Sederhana**
- Daftar presensi per tanggal
- Export to Excel (basic)

### 2.2 Non-Functional Requirements (Yang Penting)
- Responsive design (mobile-first)
- Loading time maksimal 5 detik (realistic untuk pemula)
- Bisa diakses di browser Chrome/Safari
- Data aman (basic security)

---

## 3. Teknologi Stack Sederhana

### 3.1 Backend (Familiar & Stable)
- **Framework**: Laravel 10 (mature, good documentation)
- **Template Engine**: Blade (simple, no need for separate frontend)
- **Database**: MySQL 8.0 (straightforward)
- **Authentication**: Laravel built-in auth (tidak perlu Sanctum)

### 3.2 Frontend (Simple & Effective)
- **CSS Framework**: Tailwind CSS (utility-first, modern approach)
- **JavaScript**: Alpine.js + Vanilla JS (lightweight, reactive)
- **Icons**: Font Awesome (simple implementation)
- **Camera**: WebRTC API (browser native)

### 3.3 External Services
- **Face Recognition**: Biznet Face API (sudah ada dokumentasinya)
- **Maps**: Browser Geolocation API (gratis)
- **Hosting**: Shared Hosting cPanel
- **Domain**: jakakuasanusantara.web.id

### 3.4 Development Tools
- **Local Server**: MAMP (macOS)
- **Code Editor**: PhpStorm (professional IDE)
- **Version Control**: Git (basic commands)
- **Database Tool**: phpMyAdmin

---

## 4. Fitur MVP

### 4.1 Halaman Login
**Tujuan**: Authentication sederhana

**Fitur**:
- Form login (username + password)
- Remember me checkbox
- Redirect ke dashboard setelah login
- Basic validation

**User Stories**:
- Sebagai admin, saya ingin login untuk akses dashboard
- Sebagai karyawan, saya ingin login untuk melakukan presensi

### 4.2 Dashboard Admin
**Tujuan**: Overview kehadiran hari ini

**Fitur**:
- Total karyawan hadir hari ini
- List karyawan yang sudah/belum presensi
- Jam masuk/keluar terakhir
- Link ke halaman laporan

### 4.3 Dashboard User
**Tujuan**: Interface untuk presensi

**Fitur**:
- Tombol "Clock In" / "Clock Out"
- Status presensi hari ini
- History presensi 7 hari terakhir
- Profil sederhana

### 4.4 Halaman Presensi
**Tujuan**: Capture foto dan lokasi untuk presensi

**Fitur**:
- Buka kamera untuk selfie
- Capture foto wajah
- Deteksi lokasi GPS otomatis
- Validasi lokasi (dalam radius kantor)
- Simpan presensi ke database
- Notifikasi berhasil/gagal

### 4.5 Halaman Laporan
**Tujuan**: Melihat data kehadiran

**Fitur**:
- Filter by tanggal
- Table list presensi
- Export to Excel
- Search by nama karyawan

### 4.6 Management User (Admin Only)
**Tujuan**: Kelola data karyawan

**Fitur**:
- Add new employee
- Edit employee data
- Enroll face (register wajah ke API)
- Set lokasi kerja
- Activate/deactivate user

---

## 5. Database Design

### 5.1 Tables (Minimal & Simple)

#### **users**
```
id, name, email, password, role (admin/user), 
face_id (untuk Biznet API), created_at, updated_at
```

#### **employees**
```
id, user_id, employee_id, phone, position, 
location_lat, location_lng, radius, status, 
created_at, updated_at
```

#### **attendances**
```
id, user_id, type (in/out), photo_path, 
latitude, longitude, status (success/failed), 
notes, created_at
```

#### **locations**
```
id, name, latitude, longitude, radius, 
created_at, updated_at
```

### 5.2 Relationships
- User **hasOne** Employee
- User **hasMany** Attendances  
- Employee **belongsTo** Location

---

## 6. Implementasi Step-by-Step

### 6.1 Phase 1: Basic Setup

#### **Step 1: Laravel Installation**
- Install Laravel 10 menggunakan MAMP
- Setup database connection
- Run basic migrations

#### **Step 2: Authentication Setup**
- Install Laravel Breeze (simple auth scaffold)
- Install Tailwind CSS dan Alpine.js
- Customize login/register views
- Add role field to users table

#### **Step 3: Basic Layout**
- Setup master layout dengan Tailwind CSS
- Buat navigation menu
- Responsive sidebar

### 6.2 Phase 2: Database & Models

#### **Step 4: Database Migrations**
- Buat migration untuk employees table
- Buat migration untuk attendances table
- Buat migration untuk locations table

#### **Step 5: Eloquent Models**
- Buat model Employee dengan relationships
- Buat model Attendance dengan relationships
- Buat model Location

#### **Step 6: Seeders**
- Buat seeder untuk admin user
- Buat seeder untuk sample locations
- Buat seeder untuk sample employees

### 6.3 Phase 3: Face Recognition

#### **Step 7: Biznet API Integration**
- Buat service class untuk Face API
- Implement face enrollment
- Implement face verification
- Error handling untuk API calls

#### **Step 8: Face Management**
- Halaman enroll face untuk admin
- Test face verification
- Handle API responses

### 6.4 Phase 4: Core Features

#### **Step 9: Presensi Interface**
- Halaman presensi dengan camera
- Alpine.js untuk capture photo
- Geolocation detection

#### **Step 10: Presensi Logic**
- Controller untuk handle presensi
- Validation logic
- Save to database

#### **Step 11: Dashboard**
- Admin dashboard dengan statistics
- User dashboard
- Today's attendance list

### 6.5 Phase 5: Reporting

#### **Step 12: Basic Reports**
- Attendance list dengan filter tanggal
- Search functionality
- Export to Excel using PhpSpreadsheet

#### **Step 13: UI Polish**
- Responsive design testing
- Loading states
- Success/error messages
- Basic animations

---

## 7 Error Handling

### 7.1 Common Errors to Handle
- Internet connection loss
- Camera permission denied
- GPS permission denied
- Face API quota exceeded
- Database connection error
- Invalid photo format

### 7.2 User-Friendly Messages
- "Silakan izinkan akses kamera"
- "Lokasi Anda di luar area kantor"
- "Wajah tidak terdeteksi, coba lagi"
- "Presensi berhasil disimpan"

---

## Tips untuk Pemula

### ✅ **Do's**
- Mulai dengan fitur yang paling sederhana
- Test setiap fitur setelah selesai
- Commit code secara regular ke Git
- Tulis komentar di code yang kompleks
- Backup database secara regular

### ❌ **Don'ts**  
- Jangan langsung bikin semua fitur sekaligus
- Jangan over-optimize di awal
- Jangan skip testing manual
- Jangan lupa handle error cases
- Jangan deploy tanpa backup

### 🔧 **Troubleshooting Tips**
- Selalu check Laravel log di `storage/logs/`
- Use `dd()` untuk debug variables
- Test API calls di Postman dulu
- Check browser console untuk JavaScript errors
- Google error messages dengan keyword "Laravel"

---

## Kesimpulan MVP

MVP ini fokus pada **functionality over complexity**. Dengan fitur-fitur inti yang simple tapi working, sistem sudah bisa menggantikan presensi manual dan memberikan value kepada PT. Jaka Kuasa Nusantara.

**Success Metrics MVP:**
- ✅ Karyawan bisa melakukan presensi dengan face recognition
- ✅ Admin bisa monitoring kehadiran real-time
- ✅ Sistem bisa validasi lokasi presensi
- ✅ Data presensi tersimpan dan bisa di-export
