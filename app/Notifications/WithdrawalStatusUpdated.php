<?php

namespace App\Notifications;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Withdrawal $withdrawal,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->withdrawal->status;
        $amount = number_format($this->withdrawal->amount, 0, ',', '.');

        if ($status === 'APPROVED') {
            return (new MailMessage)
                ->subject('Penarikan Simpanan Sukarela Disetujui')
                ->greeting('Halo, '.$notifiable->name.'!')
                ->line("Pengajuan penarikan simpanan sukarela Anda sebesar **Rp {$amount}** telah **disetujui**.")
                ->line('Dana akan ditransfer ke rekening berikut:')
                ->line("**{$this->withdrawal->bank_name}** — {$this->withdrawal->bank_account} a.n {$this->withdrawal->bank_account_name}")
                ->line($this->withdrawal->admin_note ? "Catatan: {$this->withdrawal->admin_note}" : '')
                ->action('Lihat Dashboard', url('/dashboard'))
                ->line('Terima kasih telah menjadi anggota koperasi yang aktif.');
        }

        return (new MailMessage)
            ->subject('Penarikan Simpanan Sukarela Ditolak')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line("Mohon maaf, pengajuan penarikan simpanan sukarela Anda sebesar **Rp {$amount}** **ditolak**.")
            ->line("Alasan: {$this->withdrawal->admin_note}")
            ->action('Lihat Dashboard', url('/dashboard'))
            ->line('Silakan hubungi pengurus koperasi jika ada pertanyaan.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'withdrawal_status_updated',
            'withdrawal_id' => $this->withdrawal->id,
            'amount' => $this->withdrawal->amount,
            'status' => $this->withdrawal->status,
            'admin_note' => $this->withdrawal->admin_note,
        ];
    }
}
