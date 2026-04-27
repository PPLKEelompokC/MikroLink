<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Akta Kesepakatan Setoran Simpanan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
            padding: 40px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #013599;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header .kop-title {
            font-size: 18px;
            font-weight: bold;
            color: #013599;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header .kop-subtitle {
            font-size: 11px;
            color: #555;
            margin-top: 4px;
        }
        .header .doc-number {
            font-size: 10px;
            color: #888;
            margin-top: 8px;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin: 24px 0;
        }
        .doc-title h1 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #013599;
        }
        .doc-title p {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
        }

        /* Section */
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #013599;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 35%;
            color: #555;
            font-weight: bold;
        }
        .info-table td:nth-child(2) {
            width: 5%;
            color: #555;
        }
        .info-table td:last-child {
            color: #1a1a1a;
        }

        /* Highlight Box */
        .highlight-box {
            background: #f0f4ff;
            border: 1px solid #c7d4f0;
            border-left: 4px solid #013599;
            border-radius: 4px;
            padding: 12px 16px;
            margin: 12px 0;
        }
        .highlight-box .amount {
            font-size: 20px;
            font-weight: bold;
            color: #013599;
        }
        .highlight-box .amount-label {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background: #e6f4ea;
            color: #1a7f37;
            border: 1px solid #a8d5b5;
        }

        /* Ketentuan */
        .ketentuan-list {
            padding-left: 0;
            list-style: none;
        }
        .ketentuan-list li {
            padding: 4px 0;
            font-size: 11px;
            color: #444;
            padding-left: 16px;
            position: relative;
        }
        .ketentuan-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #013599;
            font-weight: bold;
        }

        /* Signature */
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            padding: 8px;
            vertical-align: top;
        }
        .signature-box {
            border: 1px dashed #ccc;
            border-radius: 4px;
            padding: 12px;
        }
        .signature-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 60px;
        }
        .signature-name {
            font-size: 11px;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 6px;
            margin-top: 4px;
        }
        .signature-role {
            font-size: 10px;
            color: #666;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e0e0e0;
            padding-top: 12px;
            text-align: center;
            font-size: 9px;
            color: #999;
        }

        /* Watermark approved */
        .watermark {
            position: fixed;
            top: 35%;
            left: 10%;
            font-size: 80px;
            font-weight: bold;
            color: rgba(0, 128, 0, 0.06);
            transform: rotate(-30deg);
            text-transform: uppercase;
            letter-spacing: 10px;
            z-index: -1;
        }
    </style>
</head>
<body>

    {{-- Watermark --}}
    <div class="watermark">APPROVED</div>

    {{-- Header Kop Surat --}}
    <div class="header">
        <div class="kop-title">Koperasi MikroLink</div>
        <div class="kop-subtitle">Platform Simpan Pinjam Digital — Smart Inclusion</div>
        <div class="doc-number">
            No. Akta: AKTA/SET/{{ str_pad($deposit->id, 5, '0', STR_PAD_LEFT) }}/{{ now()->format('Y') }}
        </div>
    </div>

    {{-- Judul Dokumen --}}
    <div class="doc-title">
        <h1>Akta Kesepakatan Setoran Simpanan</h1>
        <p>Dokumen ini merupakan bukti sah atas setoran simpanan yang telah diverifikasi oleh admin koperasi.</p>
    </div>

    {{-- Data Anggota --}}
    <div class="section">
        <div class="section-title">Data Anggota</div>
        <table class="info-table">
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td>{{ $deposit->user->name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td>{{ $deposit->user->email }}</td>
            </tr>
            <tr>
                <td>ID Anggota</td>
                <td>:</td>
                <td>#{{ str_pad($deposit->user->id, 5, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td>Status Keanggotaan</td>
                <td>:</td>
                <td>Anggota Aktif</td>
            </tr>
        </table>
    </div>

    {{-- Detail Setoran --}}
    <div class="section">
        <div class="section-title">Detail Setoran</div>

        <div class="highlight-box">
            <div class="amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</div>
            <div class="amount-label">Nominal Setoran {{ $deposit->type }}</div>
        </div>

        <table class="info-table" style="margin-top: 12px;">
            <tr>
                <td>Jenis Simpanan</td>
                <td>:</td>
                <td>Simpanan {{ ucfirst(strtolower($deposit->type)) }}</td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>:</td>
                <td>{{ $deposit->created_at->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Tanggal Validasi</td>
                <td>:</td>
                <td>{{ $deposit->updated_at->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td><span class="status-badge">{{ $deposit->status }}</span></td>
            </tr>
            <tr>
                <td>Catatan Admin</td>
                <td>:</td>
                <td>{{ $deposit->admin_note ?? 'Setoran telah diverifikasi dan disetujui.' }}</td>
            </tr>
            <tr>
                <td>No. Referensi</td>
                <td>:</td>
                <td>DEP/{{ str_pad($deposit->id, 5, '0', STR_PAD_LEFT) }}/{{ $deposit->created_at->format('Ymd') }}</td>
            </tr>
        </table>
    </div>

    {{-- Ketentuan --}}
    <div class="section">
        <div class="section-title">Ketentuan & Pernyataan</div>
        <ul class="ketentuan-list">
            <li>Dokumen ini merupakan bukti sah bahwa setoran simpanan telah diterima dan diverifikasi oleh Koperasi MikroLink.</li>
            <li>Setoran yang telah diverifikasi akan langsung menambah saldo simpanan anggota sesuai jenis simpanan.</li>
            <li>Dokumen ini diterbitkan secara digital dan memiliki kekuatan hukum yang sama dengan dokumen fisik.</li>
            <li>Jika terdapat keberatan atas dokumen ini, harap menghubungi admin koperasi dalam waktu 3x24 jam.</li>
            <li>Simpanan yang telah disetor tidak dapat ditarik kecuali melalui prosedur penarikan yang berlaku.</li>
        </ul>
    </div>

    {{-- Tanda Tangan --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box">
                        <div class="signature-label">Anggota Koperasi</div>
                        <div class="signature-name">{{ $deposit->user->name }}</div>
                        <div class="signature-role">Anggota</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <div class="signature-label">Admin Koperasi MikroLink</div>
                        <div class="signature-name">Admin Koperasi</div>
                        <div class="signature-role">Validator Setoran</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem MikroLink pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 4px;">© {{ now()->year }} Koperasi MikroLink — Smart Inclusion | Dokumen Digital Terverifikasi</p>
    </div>

</body>
</html>