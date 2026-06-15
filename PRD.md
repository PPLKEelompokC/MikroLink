# Product Requirement Document (PRD) - MikroLink Features

Dokumen Kebutuhan Produk (PRD) ini merangkum spesifikasi, alur kerja, otorisasi peran (role), dan kebutuhan teknis untuk empat fitur utama dalam platform **MikroLink**.

---

## 1. Sistem KYC Digital (Digital KYC)

### 📌 Latar Belakang & Tujuan
Menjamin validitas identitas anggota koperasi untuk meminimalkan risiko penipuan serta mematuhi regulasi kepatuhan keuangan melalui verifikasi KTP/Dokumen digital secara aman dan terenkripsi.

### 👥 Peran & Otorisasi Hak Akses
* **Anggota (Role: `user`)**: Mengunggah foto KTP / dokumen identitas melalui halaman profil.
* **Admin / Manajer (Role: `admin`, `manager`, `super_admin`)**: Memvalidasi, menyetujui (`APPROVED`), atau menolak (`REJECTED`) dokumen KYC yang diajukan oleh anggota.
### 🛠️ Spesifikasi Teknis (Instruksi Eksekusi AI Agent)

1. **Skema Database (Migrations):**
   * Tambahkan kolom pada tabel `users` atau buat tabel baru `kyc_verifications` dengan struktur:
     * `id` (Primary Key)
     * `user_id` (Foreign Key -> users, Cascade)
     * `ktp_path` (string, lokasi penyimpanan file foto KTP)
     * `nik` (string, 16 digit, nullable)
     * `status` (enum: 'PENDING', 'APPROVED', 'REJECTED', default: 'PENDING')
     * `rejection_reason` (text, nullable, diisi jika status REJECTED)

2. **Alur Logika Backend (Controller & OCR Engine):**
   * Gunakan HTTP Client Laravel untuk mengintegrasikan **OCR.Space API** pada `KycController`.
   * **Endpoint API:** `https://api.ocr.space/parse/image` menggunakan parameter `language=ind`.
   * Gunakan implementasi **Regex PHP** (`preg_match('/\b\d{16}\b/')`) untuk mengekstraksi 16 digit angka NIK secara otomatis dari `ParsedText` yang dikembalikan oleh API.
   * **Constraint Keamanan:** Enkripsi nama file KTP saat disimpan di storage (`storage/app/private/kyc`) agar tidak bisa diakses publik secara langsung.

3. **Komponen Frontend (Livewire / Blade):**
   * **Sisi User:** Buat form upload file gambar (maksimal 2MB, format JPG/PNG). Ketika sukses diproses oleh OCR, field NIK harus terisi otomatis (*Auto-fill*) sebelum user melakukan submit akhir.
   * **Sisi Admin:** Buat tampilan tabel review berkas KYC status `PENDING`. Sediakan tombol aksi "Setujui" (mengubah status menjadi `APPROVED`) dan tombol "Tolak" (membuka modal untuk mengisi `rejection_reason` dan mengubah status menjadi `REJECTED`).

### 🧪 Kriteria Penerimaan (Acceptance Criteria)

* [ ] **AC-01:** User berhasil mengunggah foto KTP dan sistem tidak memicu error global jika API OCR.Space mengalami timeout (wajib implementasi try-catch).
* [ ] **AC-02:** Sistem berhasil mendeteksi string 16 digit angka sebagai NIK dan memasukkannya ke dalam database.
* [ ] **AC-03:** Admin dan Manajer dapat melihat daftar pengajuan KYC dan mengubah statusnya sesuai hak akses.
* [ ] **AC-04:** Kode baru terisolasi sepenuhnya di dalam lingkup profil/KYC dan **DILARANG** memodifikasi logika otentikasi atau fitur finansial utama lainnya demi mencegah regression error.

### ⚙️ Alur Kerja & Kebutuhan Fungsional
1. **Pengajuan Dokumen oleh Anggota:**
   * Anggota mengunggah dokumen KTP melalui komponen `kyc-verification`.
   * Foto KTP disimpan secara lokal pada disk penyimpanan direktori `kyc-docs`.
   * Status KYC awal diatur menjadi `PENDING`.
2. **Validasi oleh Admin:**
   * Dokumen masuk ke tabel rekapitulasi dokumen di dashboard admin.
   * Admin memeriksa keabsahan dokumen, lalu mengubah statusnya menjadi `APPROVED` atau `REJECTED`.
   * Jika disetujui, badge status keanggotaan di dashboard pengguna berubah menjadi **Verified (KYC)**.

### 💻 Komponen Teknis Terkait
* **Model & Migration:** `CommunityDocument` dan `add_role_to_users_table.php`
* **Controller:** `App\Http\Controllers\CommunityDocumentController`
* **Views / Livewire:**
  * View unggah: `resources/views/livewire/settings/kyc-verification.blade.php`
  * Halaman profil: `resources/views/livewire/settings/profile.blade.php`
  * Panel review admin: `resources/views/admin/docs/index.blade.php`
* **Route:** `/documents` dan `/admin/documents`

---

## 2. Sistem Ekspor Laporan Multi-Format

### 📌 Latar Belakang & Tujuan
Memfasilitasi admin dan pengurus koperasi untuk mengunduh rekapitulasi performa keuangan dan dampak sosial dalam format berkas yang fleksibel (Excel/XLSX dan CSV) untuk pelaporan eksternal maupun analisis luring.

### 👥 Peran & Otorisasi Hak Akses
* **Admin / Manajer / Super Admin**: Memiliki akses eksklusif untuk mengekspor laporan gabungan.

### ⚙️ Alur Kerja & Kebutuhan Fungsional
1. **Pemilihan Parameter Laporan:**
   * Pengguna menentukan rentang tanggal (`startDate` dan `endDate`) serta format file (`xlsx` atau `csv`) melalui widget `report-export`.
2. **Pemrosesan Ekspor:**
   * Sistem memanggil `MultiFormatExportService` yang mengambil data keuangan dan data dampak sosial dari `ReportExportRepository`.
   * Data diekspor secara bersamaan dalam format yang diinginkan menggunakan library Excel.

### 💻 Komponen Teknis Terkait
* **Service & Repository:**
  * `App\Services\MultiFormatExportService`
  * `App\Repositories\ReportExportRepository`
* **Exports Logic:**
  * `App\Exports\CombinedReportExport`
  * `App\Exports\FinancialReportExport`
  * `App\Exports\SocialImpactExport`
* **Views / Livewire:**
  * Komponen Ekspor: `resources/views/livewire/admin/report-export.blade.php`
  * Ditempatkan di halaman: `admin-dashboard.blade.php`, `dashboard.blade.php`, dan `profile.blade.php`.

---

## 3. Indeks Kepercayaan & Kelayakan (Trust Index)

### 📌 Latar Belakang & Tujuan
Menentukan kelayakan anggota untuk menerima pembiayaan/pinjaman berdasarkan rekam jejak perilaku finansial dan administratif mereka di platform koperasi secara otomatis dan objektif.

### 👥 Peran & Otorisasi Hak Akses
* **Anggota (Role: `user`)**: Memantau grafik skor indeks kepercayaan mereka di dashboard utama.
* **Admin / Manajer**: Mengubah nilai skor metrik kepercayaan setiap anggota melalui panel manajemen kepercayaan.

### ⚙️ Alur Kerja & Kebutuhan Fungsional
1. **Metrik & Bobot Perhitungan:**
   * Indeks dihitung secara otomatis berdasarkan 3 parameter utama:
     * **Partisipasi (Participation Score)**: Bobot **40%**
     * **Integritas (Integrity Score)**: Bobot **40%**
     * **Keandalan/Konsistensi (Reliability Score)**: Bobot **20%**
   * Rumus: `final_index = (participation_score * 0.4) + (integrity_score * 0.4) + (reliability_score * 0.2)`
2. **Pembaruan Skor:**
   * Saat admin memperbarui nilai menggunakan slider pada UI manajemen, model `TrustMetric` mengintersepsi event `saving` untuk menghitung ulang nilai `final_index` secara dinamis.
3. **Status Trust Level:**
   * Skor $\ge 70$: **HIGH TRUST** (Kelayakan Tinggi)
   * Skor $40 - 69$: **MODERATE** (Kelayakan Sedang)
   * Skor $< 40$: **LOW TRUST** (Kelayakan Rendah)

### 💻 Komponen Teknis Terkait
* **Model & Migration:**
  * `App\Models\TrustMetric` (Berisi method `calculateFinalIndex()`)
  * `database/migrations/2026_04_26_143523_add_score_to_trust_metrics_table.php`
* **Views / Livewire:**
  * Tampilan dashboard anggota: `resources/views/dashboard.blade.php`
  * Manajemen Admin: `resources/views/livewire/admin/trust-management.blade.php`

---

## 4. Portal Aspirasi Warga

### 📌 Latar Belakang & Tujuan
Menyediakan wadah komunikasi transparan bagi warga/anggota koperasi untuk menyuarakan aspirasi, saran, kritik, atau aduan, serta melacak respon tindak lanjut dari pengurus secara real-time.

### 👥 Peran & Otorisasi Hak Akses
* **Anggota (Role: `user`)**: Mengirimkan aspirasi baru dan memantau status tindak lanjut aduan mereka.
* **Admin / Manajer**: Membaca semua aduan warga, memperbarui status aduan (`pending`, `resolved`, atau `rejected`), serta melakukan moderasi data.

### ⚙️ Alur Kerja & Kebutuhan Fungsional
1. **Pengajuan Aspirasi:**
   * Anggota mengisi subjek, kategori aduan, dan pesan aspirasi pada formulir portal aspirasi.
2. **Pelacakan Tindak Lanjut:**
   * Aspirasi yang diajukan masuk dengan status `pending`.
   * Anggota dapat melihat riwayat pengajuan dan statusnya langsung di bawah form aduan.
3. **Penyelesaian oleh Admin:**
   * Admin meninjau aspirasi pada dashboard manajemen aduan, lalu memperbarui status menjadi `resolved` jika selesai ditangani atau `rejected` jika tidak valid.

### 💻 Komponen Teknis Terkait
* **Model & Controller:**
  * `App\Models\Aspiration`
  * `App\Http\Controllers\AspirationController`
* **Views / Livewire:**
  * Portal utama (Anggota): `resources/views/aspirationPortal.blade.php`
  * Komponen Kelola Admin (Livewire): `resources/views/livewire/admin/aspirations.blade.php`
* **Route:** Terdaftar pada prefix `/aspiration` dan `/admin/aspiration`.
