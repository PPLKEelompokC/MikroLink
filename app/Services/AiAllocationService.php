<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\FundAllocation;
use App\Models\IdleFundSnapshot;
use App\Models\Koperasi;
use App\Models\User;
use App\Notifications\FundAllocationRecommendation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AiAllocationService
{
    /**
     * Build the aggregated financial payload from the cooperative's data.
     *
     * @return array{
     *     koperasi_id: string,
     *     koperasi_name: string,
     *     total_cash_balance: float,
     *     total_outstanding_loans: float,
     *     total_pending_deposits: float,
     *     total_approved_deposits: float,
     *     operational_reserve: float,
     *     idle_fund_amount: float,
     *     latest_omzet: float,
     *     latest_credit_score: float,
     *     reserve_ratio: float,
     *     snapshot_date: string
     * }
     */
    public function buildFinancialPayload(Koperasi $koperasi): array
    {
        $reserveRatio = (float) config('services.ai_allocation.operational_reserve_ratio', 0.20);

        $totalCashBalance = (float) $koperasi->saldo_kas;

        $totalOutstandingLoans = (float) $koperasi->capitalLogs()
            ->where('type', 'Pinjaman Usaha')
            ->where('status', 'Disetujui')
            ->sum('amount');

        $totalPendingDeposits = (float) $koperasi->fresh()
            ?->loadCount(['capitalLogs as pending_count' => function ($query) {
                $query->where('status', 'Dalam Review');
            }])?->pending_count ?? 0;

        $totalApprovedDeposits = (float) Deposit::where('status', 'APPROVED')->sum('amount');

        $latestFinancialRecord = $koperasi->financialRecords()
            ->latest('record_date')
            ->first();

        $operationalReserve = $totalCashBalance * $reserveRatio;
        $idleFundAmount = max(0, $totalCashBalance - abs($totalOutstandingLoans) - $operationalReserve);

        return [
            'koperasi_id' => $koperasi->id_koperasi,
            'koperasi_name' => $koperasi->nama_koperasi,
            'total_cash_balance' => round($totalCashBalance, 2),
            'total_outstanding_loans' => round(abs($totalOutstandingLoans), 2),
            'total_pending_deposits' => round($totalPendingDeposits, 2),
            'total_approved_deposits' => round($totalApprovedDeposits, 2),
            'operational_reserve' => round($operationalReserve, 2),
            'idle_fund_amount' => round($idleFundAmount, 2),
            'latest_omzet' => round((float) ($latestFinancialRecord?->omzet ?? 0), 2),
            'latest_credit_score' => round((float) ($latestFinancialRecord?->credit_score ?? 0), 2),
            'reserve_ratio' => $reserveRatio,
            'snapshot_date' => now()->toDateString(),
        ];
    }

    /**
     * Persist a financial snapshot to the database.
     */
    public function createSnapshot(Koperasi $koperasi, array $payload): IdleFundSnapshot
    {
        return IdleFundSnapshot::updateOrCreate(
            [
                'koperasi_id' => $koperasi->id_koperasi,
                'snapshot_date' => $payload['snapshot_date'],
            ],
            [
                'total_cash_balance' => $payload['total_cash_balance'],
                'total_outstanding_loans' => $payload['total_outstanding_loans'],
                'total_pending_deposits' => $payload['total_pending_deposits'],
                'operational_reserve' => $payload['operational_reserve'],
                'idle_fund_amount' => $payload['idle_fund_amount'],
            ]
        );
    }

    /**
     * Send the financial payload to the configured AI engine and return parsed recommendations.
     *
     * @return array{recommendations: array, model_used: string}
     */
    public function requestAiRecommendation(IdleFundSnapshot $snapshot, array $payload): array
    {
        $driver = config('services.ai_allocation.driver', 'llamacpp');
        $timeout = config('services.ai_allocation.timeout', 120);

        $prompt = $this->buildPrompt($payload);

        try {
            $response = match ($driver) {
                'llamacpp' => $this->callLlamaCpp($prompt, $timeout),
                'openrouter' => $this->callOpenRouter($prompt, $timeout),
                'google' => $this->callGoogleAi($prompt, $timeout),
                default => throw new \InvalidArgumentException("Unsupported AI driver: {$driver}"),
            };

            return [
                'recommendations' => $this->parseAiResponse($response['content'], $driver),
                'model_used' => $response['model'],
            ];
        } catch (\Exception $exception) {
            Log::error('AI allocation request failed', [
                'driver' => $driver,
                'snapshot_id' => $snapshot->id,
                'error' => $exception->getMessage(),
            ]);

            return [
                'recommendations' => $this->getFallbackRecommendations($payload),
                'model_used' => 'Rule-based Fallback',
            ];
        }
    }

    /**
     * Store parsed AI recommendations as FundAllocation records.
     *
     * @param  array<int, array{category: string, amount: float, confidence: float, reasoning: string}>  $recommendations
     */
    public function storeAllocations(IdleFundSnapshot $snapshot, array $recommendations, string $modelUsed): Collection
    {
        $allocations = collect();

        foreach ($recommendations as $recommendation) {
            $allocation = FundAllocation::create([
                'koperasi_id' => $snapshot->koperasi_id,
                'snapshot_id' => $snapshot->id,
                'recommended_amount' => $recommendation['amount'],
                'allocation_category' => $recommendation['category'],
                'confidence_score' => $recommendation['confidence'],
                'reasoning' => $recommendation['reasoning'],
                'ai_model_used' => $modelUsed,
                'status' => 'pending',
            ]);

            $allocations->push($allocation);
        }

        return $allocations;
    }

    /**
     * Orchestrate the full analysis pipeline: build → snapshot → AI → store → notify.
     */
    public function analyze(Koperasi $koperasi): Collection
    {
        $payload = $this->buildFinancialPayload($koperasi);
        $snapshot = $this->createSnapshot($koperasi, $payload);

        $result = $this->requestAiRecommendation($snapshot, $payload);

        $allocations = $this->storeAllocations($snapshot, $result['recommendations'], $result['model_used']);

        $this->notifyManagers($snapshot, $allocations);

        return $allocations;
    }

    /**
     * Check if a koperasi has already been analyzed today.
     */
    public function hasRecentAnalysis(Koperasi $koperasi): bool
    {
        return FundAllocation::where('koperasi_id', $koperasi->id_koperasi)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }

    /**
     * Send database notifications to all Manajer Koperasi users.
     */
    protected function notifyManagers(IdleFundSnapshot $snapshot, Collection $allocations): void
    {
        $managers = User::where('role', 'Manajer Koperasi')->get();

        if ($managers->isEmpty()) {
            Log::warning('No Manajer Koperasi users found for fund allocation notification.');

            return;
        }

        Notification::send($managers, new FundAllocationRecommendation($snapshot, $allocations));
    }

    /**
     * Build the structured prompt for the AI engine.
     */
    protected function buildPrompt(array $payload): string
    {
        $allocationBase = $payload['idle_fund_amount'] > 0
            ? $payload['idle_fund_amount']
            : $payload['total_cash_balance'];

        $fundsDescription = $payload['idle_fund_amount'] > 0
            ? "Available Idle Funds: Rp {$payload['idle_fund_amount']}"
            : "Available Idle Funds: Rp 0 (use Total Cash Balance of Rp {$payload['total_cash_balance']} as base for strategic allocation recommendations)";

        return <<<PROMPT
You are a financial advisor AI for an Indonesian cooperative (koperasi). Analyze the following financial data and recommend strategic fund allocations.

## Financial Data
- Cooperative: {$payload['koperasi_name']}
- Total Cash Balance: Rp {$payload['total_cash_balance']}
- Outstanding Loans: Rp {$payload['total_outstanding_loans']}
- Approved Member Deposits: Rp {$payload['total_approved_deposits']}
- Operational Reserve ({$payload['reserve_ratio']}): Rp {$payload['operational_reserve']}
- {$fundsDescription}
- Latest Monthly Revenue (Omzet): Rp {$payload['latest_omzet']}
- Credit Score: {$payload['latest_credit_score']}

## Instructions
Recommend how to allocate Rp {$allocationBase} across these categories:
1. Pinjaman Usaha Mikro (Micro Business Loans)
2. Investasi Jangka Pendek (Short-term Investment)
3. Dana Cadangan Likuiditas (Liquidity Reserve Fund)
4. Program Pemberdayaan Anggota (Member Empowerment Program)

For each category provide: the recommended allocation amount in IDR, a confidence score (0-100), and a brief reasoning.

Respond ONLY with a valid JSON array. No markdown, no explanation, no other text. Example format:
[{"category": "Pinjaman Usaha Mikro", "amount": 50000000, "confidence": 85, "reasoning": "explanation here"}]
PROMPT;
    }

    /**
     * Call the local llama.cpp server (OpenAI-compatible API).
     */
    protected function callLlamaCpp(string $prompt, int $timeout): array
    {
        $baseUrl = config('services.ai_allocation.llamacpp.base_url', 'http://127.0.0.1:8080');
        $model = config('services.ai_allocation.llamacpp.model');

        $response = Http::timeout($timeout)
            ->connectTimeout(5)
            ->retry([1000, 3000])
            ->post("{$baseUrl}/v1/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a cooperative financial advisor AI. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2048,
                'stream' => false,
            ])
            ->throw();

        return [
            'content' => $response->json('choices.0.message.content', '[]'),
            'model' => $response->json('model', $model),
        ];
    }

    /**
     * Call the OpenRouter API (OpenAI-compatible).
     */
    protected function callOpenRouter(string $prompt, int $timeout): array
    {
        $baseUrl = config('services.ai_allocation.openrouter.base_url', 'https://openrouter.ai/api/v1');
        $apiKey = config('services.ai_allocation.openrouter.api_key');
        $model = config('services.ai_allocation.openrouter.model');

        if (empty($apiKey)) {
            throw new \RuntimeException('OpenRouter API key is not configured. Set AI_OPENROUTER_API_KEY in .env');
        }

        $response = Http::timeout($timeout)
            ->connectTimeout(5)
            ->retry([1000, 3000])
            ->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'HTTP-Referer' => config('app.url', 'http://localhost'),
                'X-Title' => 'MikroLink Fund Allocation',
            ])
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a cooperative financial advisor AI. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2048,
            ])
            ->throw();

        return [
            'content' => $response->json('choices.0.message.content', '[]'),
            'model' => $response->json('model', $model),
        ];
    }

    /**
     * Call the Google AI Studio (Gemini) API.
     */
    protected function callGoogleAi(string $prompt, int $timeout): array
    {
        $baseUrl = config('services.ai_allocation.google.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        $apiKey = config('services.ai_allocation.google.api_key');
        $primaryModel = config('services.ai_allocation.google.model', 'gemini-2.0-flash');
        $fallbackModels = config('services.ai_allocation.google.fallback_models', []);

        if (empty($apiKey)) {
            throw new \RuntimeException('Google AI API key is not configured. Set AI_GOOGLE_API_KEY in .env');
        }

        // Assemble model queue: primary first, then fallbacks
        $modelQueue = array_merge([$primaryModel], $fallbackModels);
        $lastModel = end($modelQueue);
        reset($modelQueue);

        foreach ($modelQueue as $model) {
            try {
                $generationConfig = [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 2048,
                ];

                // Only Gemini models support responseMimeType for structured JSON output
                if (Str::startsWith($model, 'gemini')) {
                    $generationConfig['responseMimeType'] = 'application/json';
                }

                $response = Http::timeout($timeout)
                    ->connectTimeout(5)
                    ->retry([1000, 3000])
                    ->post("{$baseUrl}/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => "You are a cooperative financial advisor AI. You MUST respond with ONLY a valid JSON array. No markdown, no explanation.\n\n{$prompt}"],
                                ],
                            ],
                        ],
                        'generationConfig' => $generationConfig,
                    ])
                    ->throw();

                // Successful response, return content and the actual model used
                return [
                    'content' => $response->json('candidates.0.content.parts.0.text', '[]'),
                    'model' => $model,
                ];
            } catch (\Throwable $e) {
                // Log the failure and continue to the next fallback model
                Log::warning('Google AI model failed, trying fallback if available', [
                    'model' => $model,
                    'error' => $e->getMessage(),
                ]);

                // If this was the last model in the queue, rethrow
                if ($model === $lastModel) {
                    throw $e;
                }
            }
        }

        // Should never reach here, but fallback just in case
        throw new \RuntimeException('All Google AI models failed');
    }

    /**
     * Parse the raw AI response text into a structured array of recommendations.
     *
     * @return array<int, array{category: string, amount: float, confidence: float, reasoning: string}>
     */
    protected function parseAiResponse(string $rawResponse, string $driver): array
    {
        $cleanedResponse = trim($rawResponse);

        if (preg_match('/\[[\s\S]*\]/', $cleanedResponse, $matches)) {
            $cleanedResponse = $matches[0];
        }

        $decoded = json_decode($cleanedResponse, true);

        if (! is_array($decoded) || empty($decoded)) {
            Log::warning('AI response could not be parsed as valid JSON', [
                'driver' => $driver,
                'raw_response' => mb_substr($rawResponse, 0, 500),
            ]);

            return [];
        }

        $recommendations = [];

        foreach ($decoded as $item) {
            if (! isset($item['category'], $item['amount'])) {
                continue;
            }

            $recommendations[] = [
                'category' => (string) $item['category'],
                'amount' => max(0, (float) ($item['amount'] ?? 0)),
                'confidence' => min(100, max(0, (float) ($item['confidence'] ?? 50))),
                'reasoning' => (string) ($item['reasoning'] ?? 'No reasoning provided.'),
            ];
        }

        return $recommendations;
    }

    /**
     * Provide rule-based fallback recommendations when AI is unavailable.
     *
     * @return array<int, array{category: string, amount: float, confidence: float, reasoning: string}>
     */
    protected function getFallbackRecommendations(array $payload): array
    {
        $baseFund = (float) $payload['idle_fund_amount'];

        // When idle funds are 0, use total cash balance as base for recommendations
        if ($baseFund <= 0) {
            $baseFund = (float) $payload['total_cash_balance'];
        }

        if ($baseFund <= 0) {
            return [];
        }

        return [
            [
                'category' => 'Pinjaman Usaha Mikro',
                'amount' => round($baseFund * 0.40, 2),
                'confidence' => 60,
                'reasoning' => 'Fallback: 40% allocated to micro business loans based on default strategy (AI unavailable).',
            ],
            [
                'category' => 'Investasi Jangka Pendek',
                'amount' => round($baseFund * 0.25, 2),
                'confidence' => 55,
                'reasoning' => 'Fallback: 25% allocated to short-term investments for yield optimization (AI unavailable).',
            ],
            [
                'category' => 'Dana Cadangan Likuiditas',
                'amount' => round($baseFund * 0.20, 2),
                'confidence' => 70,
                'reasoning' => 'Fallback: 20% retained as additional liquidity reserve (AI unavailable).',
            ],
            [
                'category' => 'Program Pemberdayaan Anggota',
                'amount' => round($baseFund * 0.15, 2),
                'confidence' => 50,
                'reasoning' => 'Fallback: 15% allocated to member empowerment programs (AI unavailable).',
            ],
        ];
    }
}
