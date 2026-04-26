<?php

namespace App\Notifications;

use App\Models\IdleFundSnapshot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class FundAllocationRecommendation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public IdleFundSnapshot $snapshot,
        public Collection $allocations,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $topAllocations = $this->allocations
            ->sortByDesc('recommended_amount')
            ->take(3)
            ->map(fn ($allocation) => [
                'category' => $allocation->allocation_category,
                'amount' => $allocation->recommended_amount,
                'confidence' => $allocation->confidence_score,
            ])
            ->values()
            ->toArray();

        $totalRecommended = $this->allocations->sum('recommended_amount');

        return [
            'type' => 'fund_allocation_recommendation',
            'title' => 'Rekomendasi Alokasi Dana Strategis',
            'message' => sprintf(
                'AI telah menganalisis dana idle sebesar Rp %s dan menghasilkan %d rekomendasi alokasi strategis.',
                number_format($this->snapshot->idle_fund_amount, 0, ',', '.'),
                $this->allocations->count()
            ),
            'snapshot_id' => $this->snapshot->id,
            'snapshot_date' => $this->snapshot->snapshot_date->toDateString(),
            'idle_fund_amount' => $this->snapshot->idle_fund_amount,
            'total_recommended' => $totalRecommended,
            'recommendation_count' => $this->allocations->count(),
            'top_allocations' => $topAllocations,
        ];
    }
}
