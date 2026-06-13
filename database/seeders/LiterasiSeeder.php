<?php

namespace Database\Seeders;

use App\Models\LiterasiArtikel;
use App\Models\User;
use Illuminate\Database\Seeder;

class LiterasiSeeder extends Seeder
{
    public function run()
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : null;

        LiterasiArtikel::insert([
            [
                'judul' => 'Cara Cerdas Menabung di Koperasi',
                'slug' => 'cara-cerdas-menabung-di-koperasi',
                'ringkasan' => 'Panduan praktis untuk anggota baru dalam memaksimalkan simpanan sukarela dan wajib.',
                'konten' => 'Isi artikel lengkap mengenai pentingnya menabung, perbedaan jenis simpanan, dan keuntungan mendapatkan Sisa Hasil Usaha (SHU) setiap tahunnya...',
                'kategori' => 'menabung',
                'thumbnail' => 'literasi/sample-1.jpg',
                'estimasi_baca' => 5,
                'level' => 'pemula',
                'is_published' => true,
                'views' => 124,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Memahami Bunga Pinjaman Menurun',
                'slug' => 'memahami-bunga-pinjaman-menurun',
                'ringkasan' => 'Apa itu bunga menurun? Pelajari cara kerja cicilan yang makin ringan setiap bulannya.',
                'konten' => 'Isi artikel lengkap yang membahas perbedaan bunga tetap dan bunga menurun. Bunga menurun sangat menguntungkan anggota karena beban bunga dihitung dari sisa pokok pinjaman...',
                'kategori' => 'pinjaman',
                'thumbnail' => 'literasi/sample-2.jpg',
                'estimasi_baca' => 7,
                'level' => 'menengah',
                'is_published' => true,
                'views' => 89,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pentingnya Dana Darurat Bagi Keluarga',
                'slug' => 'pentingnya-dana-darurat-keluarga',
                'ringkasan' => 'Jangan tunggu krisis datang. Ini alasan mengapa keluarga butuh cadangan finansial.',
                'konten' => 'Dana darurat adalah jaring pengaman. Kami menyarankan anggota memiliki simpanan setidaknya 3-6 kali pengeluaran bulanan...',
                'kategori' => 'perencanaan',
                'thumbnail' => 'literasi/sample-3.jpg',
                'estimasi_baca' => 4,
                'level' => 'pemula',
                'is_published' => true,
                'views' => 205,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
