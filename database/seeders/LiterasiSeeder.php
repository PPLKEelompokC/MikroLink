<?php

namespace Database\Seeders;

use App\Models\LiterasiArtikel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LiterasiSeeder extends Seeder
{
    public function run(): void
    {
        $artikels = [
            [
                'judul'         => 'Mengapa Menabung Sejak Dini Itu Penting?',
                'kategori'      => 'menabung',
                'level'         => 'pemula',
                'estimasi_baca' => 4,
                'ringkasan'     => 'Menabung adalah fondasi utama kebebasan finansial. Pelajari manfaat nyata menabung sejak dini dan cara memulainya dengan mudah.',
                'konten'        => '<h2>Apa Itu Menabung?</h2><p>Menabung adalah kebiasaan menyisihkan sebagian penghasilan untuk disimpan dan digunakan di masa depan.</p><h2>Manfaat Menabung Sejak Dini</h2><ul><li><strong>Dana darurat</strong> – Siap menghadapi kejadian tak terduga tanpa harus berhutang.</li><li><strong>Bebas dari jebakan utang</strong> – Orang yang punya tabungan tidak perlu meminjam untuk kebutuhan mendadak.</li><li><strong>Kekuatan bunga majemuk</strong> – Uang yang ditabung sejak muda akan tumbuh jauh lebih besar.</li></ul><h2>Rumus Sederhana: 50-30-20</h2><ul><li><strong>50%</strong> untuk kebutuhan pokok</li><li><strong>30%</strong> untuk keinginan</li><li><strong>20%</strong> untuk tabungan dan investasi</li></ul>',
            ],
            [
                'judul'         => 'Memahami Bunga Pinjaman: Flat vs Efektif',
                'kategori'      => 'pinjaman',
                'level'         => 'menengah',
                'estimasi_baca' => 6,
                'ringkasan'     => 'Sebelum mengambil pinjaman, pahami dulu perbedaan bunga flat dan efektif agar tidak terjebak cicilan yang ternyata lebih mahal.',
                'konten'        => '<h2>Bunga Flat</h2><p>Bunga dihitung dari <strong>pokok awal pinjaman</strong> sepanjang tenor. Meski angkanya terlihat kecil, ini sebenarnya lebih mahal.</p><h2>Bunga Efektif</h2><p>Bunga dihitung dari <strong>sisa pokok pinjaman</strong> setiap bulan. Total bunga yang dibayar lebih kecil.</p><h2>Mana yang Lebih Menguntungkan?</h2><p>Bunga efektif selalu lebih menguntungkan bagi peminjam. Jangan tertipu angka bunga flat yang kecil!</p>',
            ],
            [
                'judul'         => 'Cara Kerja Koperasi Simpan Pinjam',
                'kategori'      => 'koperasi',
                'level'         => 'pemula',
                'estimasi_baca' => 5,
                'ringkasan'     => 'Koperasi simpan pinjam bekerja berbeda dari bank. Pahami prinsip dasarnya agar Anda bisa memanfaatkannya secara maksimal.',
                'konten'        => '<h2>Apa Itu Koperasi Simpan Pinjam?</h2><p>Koperasi simpan pinjam adalah lembaga keuangan yang dimiliki dan dioperasikan oleh anggotanya sendiri berdasarkan prinsip gotong royong.</p><h2>Jenis Simpanan</h2><ul><li><strong>Simpanan Pokok</strong> – Dibayar sekali saat mendaftar</li><li><strong>Simpanan Wajib</strong> – Dibayar rutin setiap bulan</li><li><strong>Simpanan Sukarela</strong> – Bebas jumlah dan waktu, bisa ditarik kapan saja</li></ul>',
            ],
            [
                'judul'         => 'Investasi untuk Pemula: Mulai dari Mana?',
                'kategori'      => 'investasi',
                'level'         => 'pemula',
                'estimasi_baca' => 7,
                'ringkasan'     => 'Investasi bukan hanya untuk orang kaya. Pelajari instrumen investasi yang aman dan cocok untuk pemula dengan modal kecil.',
                'konten'        => '<h2>Mengapa Harus Berinvestasi?</h2><p>Inflasi menggerus nilai uang Anda setiap tahun. Investasi adalah cara melawan inflasi.</p><h2>Instrumen untuk Pemula</h2><ol><li><strong>Deposito</strong> – Aman, dijamin LPS</li><li><strong>Reksa Dana Pasar Uang</strong> – Risiko sangat rendah, likuid</li><li><strong>Reksa Dana Pendapatan Tetap</strong> – Imbal hasil lebih tinggi</li></ol><h2>Jauhi Investasi Bodong</h2><p>Waspadai tawaran yang menjanjikan keuntungan besar tanpa risiko. Selalu cek legalitas ke OJK.</p>',
            ],
            [
                'judul'         => 'Keluar dari Jebakan Utang: Strategi Nyata',
                'kategori'      => 'utang',
                'level'         => 'menengah',
                'estimasi_baca' => 6,
                'ringkasan'     => 'Punya banyak utang bukan akhir dari segalanya. Ada dua strategi terbukti untuk melunasi utang lebih cepat.',
                'konten'        => '<h2>Metode Snowball</h2><p>Lunasi utang dari <strong>jumlah terkecil</strong> terlebih dahulu. Memberikan motivasi cepat.</p><h2>Metode Avalanche</h2><p>Lunasi utang dengan <strong>bunga tertinggi</strong> terlebih dahulu. Menghemat lebih banyak bunga.</p><h2>Tips Tambahan</h2><ul><li>Stop menambah utang baru selama masa pelunasan</li><li>Cari penghasilan tambahan untuk mempercepat pelunasan</li></ul>',
            ],
            [
                'judul'         => 'Membuat Anggaran Keluarga yang Realistis',
                'kategori'      => 'perencanaan',
                'level'         => 'pemula',
                'estimasi_baca' => 5,
                'ringkasan'     => 'Anggaran bukan tentang pembatasan, melainkan tentang memberi setiap rupiah tujuan yang jelas.',
                'konten'        => '<h2>Langkah Membuat Anggaran</h2><ol><li>Hitung total penghasilan</li><li>Catat semua pengeluaran wajib</li><li>Tetapkan target tabungan di awal bulan</li><li>Alokasikan sisanya untuk kebutuhan lain</li></ol><h2>Metode Amplop</h2><p>Siapkan amplop untuk setiap kategori pengeluaran dan isi dengan uang tunai sesuai anggaran.</p>',
            ],
        ];

        foreach ($artikels as $data) {
            LiterasiArtikel::create([
                'judul'         => $data['judul'],
                'slug'          => Str::slug($data['judul']),
                'ringkasan'     => $data['ringkasan'],
                'konten'        => $data['konten'],
                'kategori'      => $data['kategori'],
                'estimasi_baca' => $data['estimasi_baca'],
                'level'         => $data['level'],
                'is_published'  => true,
            ]);
        }
    }
}