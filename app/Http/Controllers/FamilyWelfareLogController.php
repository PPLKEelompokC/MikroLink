<?php

namespace App\Http\Controllers;

use App\Models\FamilyWelfareLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FamilyWelfareLogController extends Controller
{
    public function dashboard(Request $request): View
    {
        $logs = FamilyWelfareLog::query()
            ->where('user_id', $request->user()->id)
            ->latest('period_date')
            ->get();

        $latestLog = $logs->first();
        $oldestLog = $logs->last();

        $summary = [
            'total_logs' => $logs->count(),
            'latest_score' => $latestLog?->welfare_score,
            'latest_income' => $latestLog?->income_after,
            'income_growth' => $latestLog ? $latestLog->incomeGrowthPercentage() : 0,
            'trend_growth' => $latestLog && $oldestLog && $latestLog->id !== $oldestLog->id
                ? $this->calculateGrowth((float) $oldestLog->income_after, (float) $latestLog->income_after)
                : null,
        ];

        return view('user.welfare.dashboard', compact('logs', 'summary'));
    }

    public function create(): View
    {
        return view('user.welfare.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'period_date' => ['required', 'date', 'before_or_equal:today'],
            'income_before' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'income_after' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'dependents_count' => ['required', 'integer', 'min:0', 'max:30'],
            'food_security_status' => ['required', Rule::in(['tercukupi', 'rentan', 'kurang'])],
            'education_access_status' => ['required', Rule::in(['baik', 'terbatas', 'terhambat'])],
            'health_access_status' => ['required', Rule::in(['baik', 'terbatas', 'terhambat'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        FamilyWelfareLog::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'welfare_score' => $this->calculateWelfareScore($validated),
        ]);

        return redirect()
            ->route('welfare.dashboard')
            ->with('success', 'Kuesioner kesejahteraan keluarga berhasil disimpan.');
    }

    /**
     * @param  array{income_before: numeric, income_after: numeric, food_security_status: string, education_access_status: string, health_access_status: string}  $data
     */
    private function calculateWelfareScore(array $data): int
    {
        $score = 40;

        $incomeGrowth = $this->calculateGrowth((float) $data['income_before'], (float) $data['income_after']);
        $score += match (true) {
            $incomeGrowth >= 25 => 25,
            $incomeGrowth >= 10 => 18,
            $incomeGrowth >= 0 => 10,
            default => 0,
        };

        $score += match ($data['food_security_status']) {
            'tercukupi' => 15,
            'rentan' => 8,
            default => 0,
        };

        $score += match ($data['education_access_status']) {
            'baik' => 10,
            'terbatas' => 5,
            default => 0,
        };

        $score += match ($data['health_access_status']) {
            'baik' => 10,
            'terbatas' => 5,
            default => 0,
        };

        return min(100, max(0, $score));
    }

    private function calculateGrowth(float $baseValue, float $currentValue): float
    {
        if ($baseValue <= 0) {
            return $currentValue > 0 ? 100.0 : 0.0;
        }

        return round((($currentValue - $baseValue) / $baseValue) * 100, 1);
    }
}
