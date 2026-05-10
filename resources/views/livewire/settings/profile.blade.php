<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full max-w-5xl mx-auto space-y-8 pb-20">
    @include('partials.settings-heading')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        {{-- Left Side: Basic Info & KYC --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Card 1: Informasi Dasar --}}
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Dasar</h3>
                    <p class="text-sm text-gray-500">Data profil keanggotaan Anda di MikroLink.</p>
                </div>
                <div class="p-8">
                    <form wire:submit="updateProfileInformation" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:input wire:model="name" label="{{ __('Name') }}" type="text" name="name" required autofocus />
                            <flux:input wire:model="email" label="{{ __('Email') }}" type="email" name="email" required />
                        </div>

                        <div class="flex items-center gap-4">
                            <flux:button variant="primary" type="submit" class="bg-amber-500 hover:bg-amber-600 border-none shadow-lg shadow-amber-100 px-8">
                                {{ __('Simpan Profil') }}
                            </flux:button>
                            <x-action-message on="profile-updated">
                                {{ __('Berhasil disimpan.') }}
                            </x-action-message>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card 2: KYC Verification (PBI-01) --}}
            <livewire:settings.kyc-verification />
        </div>

        {{-- Right Side: Security --}}
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="text-lg font-bold text-gray-900">Keamanan</h3>
                    <p class="text-sm text-gray-500">Proteksi akun & pengaturan privasi.</p>
                </div>
                <div class="p-8 space-y-8">
                    <livewire:settings.password />
                    
                    <flux:separator variant="subtle" />
                    
                    <div class="pt-4">
                        <livewire:settings.delete-user-form />
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="bg-indigo-900 rounded-[32px] p-8 text-white relative overflow-hidden shadow-xl">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative">
                    <svg class="w-8 h-8 text-indigo-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <h4 class="font-bold mb-2">Privasi Terjamin</h4>
                    <p class="text-xs text-indigo-200 leading-relaxed">Seluruh data identitas dan transaksi Anda dilindungi dengan enkripsi tingkat tinggi sesuai standar keamanan perbankan.</p>
                </div>
            </div>
        </div>
    </div>
</section>
