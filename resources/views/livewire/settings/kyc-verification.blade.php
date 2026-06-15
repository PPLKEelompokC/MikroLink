<?php

use App\Models\KycVerification;
use App\Models\CommunityDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $ktp_photo;
    public $nik = '';
    public $fullName = '';
    public $status = 'Belum Verifikasi';
    public $isProcessing = false;
    public $note = '';
    public $ktp_path = '';
    public $hasStoredKtp = false;
    public $ocrRawText = '';

    public function mount()
    {
        $this->fullName = Auth::user()->name;

        // Check new table first
        $kyc = KycVerification::where('user_id', Auth::id())->first();
        if ($kyc) {
            $this->nik = $kyc->nik;
            $this->ktp_path = $kyc->ktp_path;
            $this->hasStoredKtp = !empty($kyc->ktp_path);
            
            if ($kyc->status === 'APPROVED') {
                $this->status = 'Terverifikasi';
            } elseif ($kyc->status === 'REJECTED') {
                $this->status = 'Ditolak';
                $this->note = $kyc->rejection_reason;
            } else {
                $this->status = 'Sedang Diproses';
            }
            return;
        }

        // Fallback to legacy document
        $doc = CommunityDocument::where('user_id', Auth::id())
            ->where('document_name', 'KTP')
            ->first();

        if ($doc) {
            if ($doc->status === 'approved') {
                $this->status = 'Terverifikasi';
            } elseif ($doc->status === 'rejected') {
                $this->status = 'Ditolak';
                $this->note = $doc->note;
            } else {
                $this->status = 'Sedang Diproses';
            }
            
            $data = json_decode($doc->note, true);
            if (is_array($data)) {
                $this->nik = $data['nik'] ?? '';
            }
        }
    }

    /**
     * Extract NIK from OCR text with robust cleaning.
     * OCR often misreads digits as letters (e.g., 0->O, 1->l, 6->b, 5->S).
     */
    private function extractNik(string $parsedText): ?string
    {
        // Strategy 1: Look for a line near "NIK" label that contains mostly digits
        $lines = preg_split('/[\r\n]+/', $parsedText);
        $nikCandidate = null;
        $foundNikLabel = false;

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Detect if this is the NIK label line
            if (preg_match('/^NIK/i', $line)) {
                $foundNikLabel = true;
                // Check if the NIK value is on the same line (e.g., "NIK : 3203012503770016")
                $cleaned = preg_replace('/^NIK\s*:?\s*/i', '', $line);
                $cleaned = preg_replace('/[^0-9]/', '', $cleaned);
                if (strlen($cleaned) >= 16) {
                    return substr($cleaned, 0, 16);
                }
                continue;
            }

            // The line after "NIK" label is likely the NIK value
            if ($foundNikLabel && strlen($line) > 0) {
                // Clean OCR artifacts: replace common misreads
                $cleaned = $line;
                $cleaned = str_replace(['O', 'o', 'l', 'I', 'b', 'B', 'S', 's', 'G', 'g', 'D', 'Z', 'z'], 
                                       ['0', '0', '1', '1', '6', '8', '5', '5', '6', '9', '0', '2', '2'], $cleaned);
                $cleaned = preg_replace('/[^0-9]/', '', $cleaned);
                
                if (strlen($cleaned) >= 16) {
                    return substr($cleaned, 0, 16);
                }
                $foundNikLabel = false;
            }
        }

        // Strategy 2: Find any 16-digit sequence in the entire cleaned text
        $cleanText = str_replace([' ', "\r", "\n", "\t"], '', $parsedText);
        if (preg_match('/(?<!\d)\d{16}(?!\d)/', $cleanText, $matches)) {
            return $matches[0];
        }

        // Strategy 3: Find near-16-digit sequences and fix OCR misreads
        foreach ($lines as $line) {
            $line = trim($line);
            // Skip lines that look like labels or short text
            if (strlen($line) < 10) continue;
            
            // Count how many chars are digits
            $digitCount = preg_match_all('/\d/', $line);
            $totalLen = strlen(preg_replace('/\s/', '', $line));
            
            // If >80% are digits and total length is around 16, this is likely NIK
            if ($totalLen >= 14 && $totalLen <= 20 && $digitCount >= 10) {
                $cleaned = $line;
                $cleaned = str_replace(['O', 'o', 'l', 'I', 'b', 'B', 'S', 's', 'G', 'g', 'D', 'Z', 'z'],
                                       ['0', '0', '1', '1', '6', '8', '5', '5', '6', '9', '0', '2', '2'], $cleaned);
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
     * So the name is always the first value line after the NIK number.
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
                // Remove leading colon if present
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
            // Look at lines after NIK for the first colon-prefixed value that looks like a name
            for ($i = $nikLineIndex + 1; $i < min($nikLineIndex + 5, count($lines)); $i++) {
                $line = trim($lines[$i]);
                if (str_starts_with($line, ':')) {
                    $val = trim(substr($line, 1));
                    if (strlen($val) > 1 && $this->looksLikeName($val)) {
                        return $this->cleanName($val);
                    }
                }
                // Non-colon line that is mostly uppercase letters (name without colon prefix)
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
        // Reject if it looks like a date (contains digits with separators)
        if (preg_match('/\d{2}[\.\-\/]\d{2}[\.\-\/]\d{2,4}/', $val)) {
            return false;
        }
        // Reject if it looks like an address (contains JL, JALAN, RT, RW, DESA, KEL, KEC, PERUMAHAN, BLOK, NO)
        if (preg_match('/\b(JL|JALAN|RT|RW|DESA|KEL|KEC|PERUMAHAN|BLOK|NO|GG|GANG)\b/i', $val)) {
            return false;
        }
        // Reject if it looks like blood type or gender
        if (preg_match('/^(LAKI|PEREMPUAN|ISLAM|KRISTEN|KATOLIK|HINDU|BUDHA|MARRIED|OTHERS|CHRISTIAN)/i', $val)) {
            return false;
        }
        // Must contain at least one letter
        if (!preg_match('/[a-zA-Z]/', $val)) {
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

    public function processKtp()
    {
        $this->validate(['ktp_photo' => 'required|image|max:2048']);
        $this->isProcessing = true;

        try {
            $extension = $this->ktp_photo->getClientOriginalExtension();
            if (empty($extension)) {
                $extension = 'jpg';
            }
            $encryptedName = md5(uniqid() . time()) . '.' . $extension;
            $path = $this->ktp_photo->storeAs('kyc', $encryptedName, 'local');

            $fileContent = Storage::disk('local')->get($path);

            $response = Http::asMultipart()
                ->timeout(30)
                ->attach('file', $fileContent, $encryptedName)
                ->post('https://api.ocr.space/parse/image?apikey=' . (config('services.ocr_space.key') ?? 'K83739044788957') . '&language=ind');

            if ($response->successful()) {
                $data = $response->json();
                $parsedText = $data['ParsedResults'][0]['ParsedText'] ?? '';

                Log::info('OCR KYC ParsedText: ' . $parsedText);

                $this->ocrRawText = $parsedText;

                // Extract NIK with robust OCR error correction
                $extractedNik = $this->extractNik($parsedText);

                // Extract Name
                $extractedName = $this->extractName($parsedText, $extractedNik);

                if ($extractedNik) {
                    $this->nik = $extractedNik;
                }
                if ($extractedName) {
                    $this->fullName = $extractedName;
                }
                $this->ktp_path = $path;
            } else {
                Log::error('OCR API failed: ' . $response->body());
                $this->addError('ktp_photo', 'Gagal memproses gambar dari OCR API.');
            }
        } catch (\Exception $e) {
            Log::error('OCR KYC Verification error: ' . $e->getMessage());
            $this->addError('ktp_photo', 'Terjadi kesalahan sistem saat memproses OCR KTP.');
        }

        $this->isProcessing = false;
    }

    public function submitKyc()
    {
        $this->validate([
            'nik' => 'required|digits:16',
            'ktp_path' => 'required|string',
        ]);

        KycVerification::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'ktp_path' => $this->ktp_path,
                'nik' => $this->nik,
                'status' => 'PENDING',
                'rejection_reason' => null,
            ]
        );

        $this->status = 'Sedang Diproses';
        $this->hasStoredKtp = true;
        $this->dispatch('kyc-updated');
    }
}; ?>

<div 
    class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden"
    x-data="{ previewUrl: null }"
    x-on:livewire-upload-start="previewUrl = null"
>
    <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Digital KYC - Verifikasi KTP</h3>
                <p class="text-sm text-gray-500">Gunakan foto KTP yang jelas untuk pembacaan otomatis.</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-[11px] font-extrabold uppercase tracking-widest 
                {{ $status === 'Terverifikasi' ? 'bg-emerald-100 text-emerald-700' : ($status === 'Sedang Diproses' ? 'bg-amber-100 text-amber-700' : ($status === 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600')) }}">
                {{ $status }}
            </span>
        </div>
    </div>

    <div class="p-8">
        @if($status === 'Belum Verifikasi' || $status === 'Ditolak')
            @if ($status === 'Ditolak' && $note)
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl text-sm font-bold">
                    Alasan Penolakan: {{ $note }}
                </div>
            @endif

            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-3xl p-10 bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                <input 
                    type="file" 
                    wire:model="ktp_photo" 
                    class="hidden" 
                    id="ktp_upload" 
                    accept="image/jpeg,image/png,image/jpg"
                    x-on:change="
                        const file = $event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => { previewUrl = e.target.result; };
                            reader.readAsDataURL(file);
                        }
                    "
                >
                
                {{-- JavaScript-based preview (bypasses Livewire temporaryUrl issues) --}}
                <template x-if="previewUrl">
                    <div class="relative w-full max-w-xs mb-4">
                        <img :src="previewUrl" class="rounded-2xl shadow-lg border-4 border-white w-full">
                        <button 
                            wire:click="$set('ktp_photo', null)" 
                            x-on:click="previewUrl = null"
                            class="absolute -top-3 -right-3 bg-red-500 text-white p-1.5 rounded-full shadow-lg"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
                
                <template x-if="!previewUrl">
                    <label for="ktp_upload" class="cursor-pointer flex flex-col items-center">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-sm font-bold text-gray-700">Pilih Foto KTP</span>
                        <span class="text-xs text-gray-400 mt-1">Format JPG/PNG, maksimal 2MB</span>
                    </label>
                </template>

                @if ($ktp_photo && !$ktp_path)
                    <button wire:click="processKtp" wire:loading.attr="disabled" class="mt-6 px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 shadow-lg transition-all flex items-center gap-3">
                        <span wire:loading.remove wire:target="processKtp">Mulai Baca Data</span>
                        <span wire:loading wire:target="processKtp" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Menganalisis KTP...
                        </span>
                    </button>
                @endif
                
                @error('ktp_photo')
                    <p class="mt-4 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if($nik || $ktp_path)
            <div class="mt-10 space-y-6">
                <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    <div>
                        <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1 block">NIK</label>
                        <input type="text" wire:model="nik" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-900 focus:ring-indigo-500" placeholder="16 digit NIK">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1 block">Nama Lengkap</label>
                        <input type="text" wire:model="fullName" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-900 focus:ring-indigo-500">
                    </div>
                </div>

                @if(!$nik)
                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-sm text-amber-700 font-semibold flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        NIK tidak terdeteksi otomatis. Silakan isi NIK secara manual (16 digit).
                    </div>
                @endif

                <button wire:click="submitKyc" wire:loading.attr="disabled" class="w-full py-4 bg-emerald-600 text-white font-extrabold rounded-2xl hover:bg-emerald-700 shadow-xl transition-all">
                    Konfirmasi & Kirim
                </button>
            </div>
            @endif
        @else
            {{-- Status: Sedang Diproses / Terverifikasi --}}
            <div class="space-y-6">
                {{-- User info card --}}
                <div class="bg-gray-50 p-6 rounded-[24px] border border-gray-100 flex items-center justify-between text-left">
                    <div>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Status Identitas</p>
                        <h4 class="text-xl font-extrabold text-gray-900 mt-1">{{ $fullName }}</h4>
                        <p class="text-sm font-bold text-indigo-600 tracking-wider mt-0.5">{{ $nik }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-600">
                        @if($status === 'Terverifikasi')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @else
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v6l4 2"/></svg>
                        @endif
                    </div>
                </div>

                {{-- KTP Preview --}}
                @if($hasStoredKtp)
                    <div class="rounded-[24px] border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                            <h4 class="text-sm font-extrabold text-gray-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Pratinjau Foto KTP
                            </h4>
                        </div>
                        <div class="p-6 flex justify-center bg-white">
                            <img 
                                src="{{ route('kyc.view.own') }}" 
                                alt="Foto KTP" 
                                class="max-w-full max-h-[320px] rounded-xl shadow-md border-2 border-gray-100 object-contain"
                            >
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
