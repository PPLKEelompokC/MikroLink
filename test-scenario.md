# Skenario Pengujian (Test Case) - Fitur Keamanan Akses & Masking Data Sensitif

Dokumen ini berisi panduan *User Acceptance Testing* (UAT) dan pengujian fungsional untuk memverifikasi keamanan hak akses (*Gate*) serta fitur penyensoran data (*Masking*) yang baru diimplementasikan.

## Konteks Fitur
1. **Gate Authorization**: Hak akses `view-sensitive-data` dan `manage-users` (Role `admin` dan `manager`).
2. **Data Masking**: *Custom Cast* model `User` yang menyembunyikan email dan data teks sensitif lainnya bila dilihat oleh user tanpa hak akses.

---

## 🧪 Skenario 1: Verifikasi Masking Berdasarkan Role (UI / API)

**Tujuan:** Memastikan email hanya disensor untuk pengguna tanpa role `admin` / `manager`.

| ID Test | Aktor / Kondisi | Langkah Pengujian | Hasil yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- |
| **TC-01** | User Biasa (`role: user`) | 1. Login menggunakan akun dengan role `user`. <br> 2. Buka halaman profile atau halaman yang menampilkan data email user lain/sendiri. | Email tampil tersensor (contoh: `al***@gmail.com`). | [ ] |
| **TC-02** | User Admin (`role: admin`) | 1. Login menggunakan akun dengan role `admin`. <br> 2. Buka halaman yang mendisplay daftar user atau email. | Email tampil utuh tanpa sensor (contoh: `alexander@gmail.com`). | [ ] |
| **TC-03** | User Manager (`role: manager`) | 1. Login menggunakan akun dengan role `manager`. <br> 2. Buka halaman yang mendisplay daftar user atau email. | Email tampil utuh tanpa sensor (contoh: `alexander@gmail.com`). | [ ] |
| **TC-04** | *Guest* (Belum Login) | 1. Akses API public atau halaman view data tanpa otentikasi login. | Email tampil tersensor. | [ ] |

---

## 🧪 Skenario 2: Pencegahan Korupsi Data (*Data Integrity*) Pada Form Update

**Tujuan:** Memastikan mekanisme penyensoran tidak merusak data asli di database ketika user menyimpan konfigurasinya ulang (menghindari email `al***@gmail.com` secara tidak sengaja tersimpan ke DB).

| ID Test | Aktor / Kondisi | Langkah Pengujian | Hasil yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- |
| **TC-05** | User Biasa (`role: user`) | 1. Login sebagai `user`. <br> 2. Buka form edit entitas yang mengembalikan email tersensor. <br> 3. Tekan *Save* / *Submit* tanpa mengganti isi kolom text email. | Data lama (asli) di DB tidak tertimpa oleh email bintang `***`. | [ ] |
| **TC-06** | User Biasa (`role: user`) | 1. Login sebagai `user`. <br> 2. Buka form edit, hapus email, masukkan email utuh baru (misal: `baru@gmail.com`). <br> 3. Tekan *Save*. | Email berhasil diupdate dengan data utuh yang baru, DB menyimpan data valid. | [ ] |

---

## 🧪 Skenario 3: Pengujian Menggunakan Laravel Tinker (CLI)

Anda bisa menjalankan test ini dengan sangat cepat di terminal server untuk memverifikasi fungsionalitas model secara langsung menggunakan perintah: `php artisan tinker`.

> **Catatan Tambahan**: 
> Saat dijalankan melalui console (CLI), perlindungan `app()->runningInConsole()` akan membuat data dikembalikan secara utuh agar memudahkan proses administrasi backend dari server.

**Test Langkah Bypassing Masking di Console:**
```php
// 1. Ambil sembarang user
$user = User::first();

// 2. Periksa output emailnya. Harus tampil format ASLI
echo $user->email;
// Output Expected: "budi@gmail.com" (Bukan "bu***@gmail.com")

// 3. Tes helper traits secara mandiri:
$trait = new class { use \App\Traits\MaskSensitiveData; };
echo $trait->maskString('1234567890', 2, 2);
// Output Expected: "12******90"
```

---

## 🧪 Skenario 4: Memastikan Proteksi Route Akses `CheckRole` & `Gate`

**Tujuan:** Gate yang didaftarkan di AppServiceProvider bekerja memblokir tindakan ilegal.

| ID Test | Aktor / Kondisi | Langkah Pengujian | Hasil yang Diharapkan (Expected Result) | Status |
| :--- | :--- | :--- | :--- | :--- |
| **TC-07** | User Biasa | Mengeksekusi function / policy yg dilindungi oleh `Gate::authorize('manage-users')`. | Mengembalikan tipe *Exception* / Response `403 Forbidden` / `Unauthorized`. | [ ] |
| **TC-08** | User Admin | Mengeksekusi function / policy yg dilindungi oleh `Gate::authorize('manage-users')`. | Aksi/Proses lolos (*Allowed*). | [ ] |
