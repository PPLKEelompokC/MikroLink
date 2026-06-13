<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    /**
     * Handle KTP upload, save it securely, and call OCR.Space API.
     */
    public function uploadKtp(Request $request): JsonResponse
    {
        $request->validate([
            'ktp_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        try {
            $file = $request->file('ktp_photo');
            $extension = $file->getClientOriginalExtension();
            $encryptedName = md5(uniqid().time()).'.'.$extension;
            $fileContent = $file->getContent() ?: 'dummy_content';

            // The local disk root points to storage/app/private, so we store directly to 'kyc'
            $path = $file->storeAs('kyc', $encryptedName, 'local');

            // Send apikey and language in the URL query string as required by OCR.space multipart protocol
            $response = Http::asMultipart()
                ->timeout(30)
                ->attach('file', $fileContent, $encryptedName)
                ->post('https://api.ocr.space/parse/image?apikey='.(config('services.ocr_space.key') ?? 'K83739044788957').'&language=ind');

            if ($response->successful()) {
                $data = $response->json();
                $parsedText = $data['ParsedResults'][0]['ParsedText'] ?? '';

                Log::info('OCR KYC ParsedText: '.$parsedText);

                $nik = $this->extractNik($parsedText);
                $fullName = $this->extractName($parsedText, $nik);

                return response()->json([
                    'success' => true,
                    'nik' => $nik,
                    'nama_lengkap' => $fullName,
                    'ktp_path' => $path,
                ]);
            }

            Log::error('OCR.Space API request failed: '.$response->body());
        } catch (\Exception $e) {
            Log::error('OCR KYC Verification error: '.$e->getMessage());
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal memproses OCR KTP.',
        ], 500);
    }

    /**
     * Extract NIK from OCR text with robust cleaning.
     * OCR often misreads digits as letters (e.g., 0->O, 1->l, 6->b, 5->S).
     */
    private function extractNik(string $parsedText): ?string
    {
        $lines = preg_split('/[\r\n]+/', $parsedText);
        $foundNikLabel = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^NIK/i', $line)) {
                $foundNikLabel = true;
                $cleaned = preg_replace('/^NIK\s*:?\s*/i', '', $line);
                $cleaned = preg_replace('/[^0-9]/', '', $cleaned);
                if (strlen($cleaned) >= 16) {
                    return substr($cleaned, 0, 16);
                }

                continue;
            }

            if ($foundNikLabel && strlen($line) > 0) {
                $cleaned = str_replace(
                    ['O', 'o', 'l', 'I', 'b', 'B', 'S', 's', 'G', 'g', 'D', 'Z', 'z'],
                    ['0', '0', '1', '1', '6', '8', '5', '5', '6', '9', '0', '2', '2'],
                    $line
                );
                $cleaned = preg_replace('/[^0-9]/', '', $cleaned);

                if (strlen($cleaned) >= 16) {
                    return substr($cleaned, 0, 16);
                }
                $foundNikLabel = false;
            }
        }

        $cleanText = str_replace([' ', "\r", "\n", "\t"], '', $parsedText);
        if (preg_match('/(?<!\d)\d{16}(?!\d)/', $cleanText, $matches)) {
            return $matches[0];
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) < 10) {
                continue;
            }

            $digitCount = preg_match_all('/\d/', $line);
            $totalLen = strlen(preg_replace('/\s/', '', $line));

            if ($totalLen >= 14 && $totalLen <= 20 && $digitCount >= 10) {
                $cleaned = str_replace(
                    ['O', 'o', 'l', 'I', 'b', 'B', 'S', 's', 'G', 'g', 'D', 'Z', 'z'],
                    ['0', '0', '1', '1', '6', '8', '5', '5', '6', '9', '0', '2', '2'],
                    $line
                );
                $cleaned = preg_replace('/[^0-9]/', '', $cleaned);
                if (strlen($cleaned) >= 16) {
                    return substr($cleaned, 0, 16);
                }
            }
        }

        return null;
    }

    /**
     * Extract name from OCR text.
     * KTP format is standardized: NIK → Nama → TTL → JK → Alamat
     */
    private function extractName(string $parsedText, ?string $nik = null): ?string
    {
        $lines = preg_split('/[\r\n]+/', $parsedText);

        // Strategy 1: Look for explicit "Nama" label
        $foundNamaLabel = false;
        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^Nama\s*:?\s*(.+)/i', $line, $m)) {
                $name = trim($m[1]);
                $name = ltrim($name, ': ');
                if (strlen($name) > 1 && $this->looksLikeName($name)) {
                    return $this->cleanName($name);
                }
                $foundNamaLabel = true;

                continue;
            }

            if ($foundNamaLabel && strlen($line) > 0) {
                $name = str_starts_with($line, ':') ? trim(substr($line, 1)) : $line;
                if (strlen($name) > 1 && $this->looksLikeName($name)) {
                    return $this->cleanName($name);
                }
                $foundNamaLabel = false;
            }
        }

        // Strategy 2: Find NIK line position, take the first colon-value after it as Nama
        $nikLineIndex = null;

        if ($nik) {
            $shortNik = substr($nik, 0, 10);
            foreach ($lines as $i => $line) {
                $cleanedLine = preg_replace('/[^0-9]/', '', $line);
                if (str_contains($cleanedLine, $shortNik)) {
                    $nikLineIndex = $i;
                    break;
                }
            }
        }

        if ($nikLineIndex === null) {
            foreach ($lines as $i => $line) {
                $line = trim($line);
                $digits = preg_replace('/[^0-9]/', '', $line);
                if (strlen($digits) >= 14 && strlen($line) <= 30) {
                    $nikLineIndex = $i;
                    break;
                }
            }
        }

        if ($nikLineIndex !== null) {
            for ($i = $nikLineIndex + 1; $i < min($nikLineIndex + 5, count($lines)); $i++) {
                $line = trim($lines[$i]);
                if (str_starts_with($line, ':')) {
                    $val = trim(substr($line, 1));
                    if (strlen($val) > 1 && $this->looksLikeName($val)) {
                        return $this->cleanName($val);
                    }
                }
                if (preg_match('/^[A-Z\s\.\,\']{2,}$/', $line) && $this->looksLikeName($line)) {
                    return $this->cleanName($line);
                }
            }
        }

        return null;
    }

    /**
     * Check if a string looks like a person name (not an address, date, or other field).
     */
    private function looksLikeName(string $val): bool
    {
        $val = trim($val);
        if (preg_match('/\d{2}[\.\-\/]\d{2}[\.\-\/]\d{2,4}/', $val)) {
            return false;
        }
        if (preg_match('/\b(JL|JALAN|RT|RW|DESA|KEL|KEC|PERUMAHAN|BLOK|NO|GG|GANG)\b/i', $val)) {
            return false;
        }
        if (preg_match('/^(LAKI|PEREMPUAN|ISLAM|KRISTEN|KATOLIK|HINDU|BUDHA|MARRIED|OTHERS|CHRISTIAN)/i', $val)) {
            return false;
        }
        if (! preg_match('/[a-zA-Z]/', $val)) {
            return false;
        }

        return true;
    }

    private function cleanName(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/[^a-zA-Z\s\.\,\']/', '', $name);

        return trim(strtoupper($name));
    }

    /**
     * Submit final KYC verification details.
     */
    public function submitKyc(Request $request): JsonResponse
    {
        $request->validate([
            'nik' => ['required', 'digits:16'],
            'ktp_path' => ['required', 'string'],
        ]);

        KycVerification::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'ktp_path' => $request->ktp_path,
                'nik' => $request->nik,
                'status' => 'PENDING',
                'rejection_reason' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi KYC berhasil dikirim.',
        ]);
    }

    /**
     * Admin/Manager action: approve KYC verification.
     */
    public function approve($id)
    {
        $verification = KycVerification::findOrFail($id);
        $verification->update([
            'status' => 'APPROVED',
            'rejection_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Verifikasi KYC disetujui.');
    }

    /**
     * Admin/Manager action: reject KYC verification.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $verification = KycVerification::findOrFail($id);
        $verification->update([
            'status' => 'REJECTED',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Verifikasi KYC ditolak.');
    }

    /**
     * Stream KTP image securely (admin access).
     */
    public function viewKtp($id)
    {
        $verification = KycVerification::findOrFail($id);

        if (! Storage::disk('local')->exists($verification->ktp_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($verification->ktp_path);
    }

    /**
     * Stream own KTP image securely (authenticated user).
     */
    public function viewOwnKtp()
    {
        $verification = KycVerification::where('user_id', auth()->id())->firstOrFail();

        if (! Storage::disk('local')->exists($verification->ktp_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($verification->ktp_path);
    }
}
