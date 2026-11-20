<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertCurrencyRequest;
use App\Models\ConversionHistory;
use App\Services\FrankfurterService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class CurrencyConverterController extends Controller
{
    public function __construct(private readonly FrankfurterService $frankfurterService) {}

    public function index(Request $request): View
    {
        return view('converter.index', array_merge($this->viewData(), [
            'histories' => $this->recentHistory($request),
        ]));
    }

    public function convert(ConvertCurrencyRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $sourceCurrency = (string) $validated['from'];
        $targetCurrency = (string) $validated['to'];

        try {
            $conversion = $this->frankfurterService->convert($amount, $sourceCurrency, $targetCurrency);
        } catch (ConnectionException|RequestException|RuntimeException $exception) {
            Log::error('Currency conversion failed.', [
                'from' => $sourceCurrency,
                'to' => $targetCurrency,
                'message' => $exception->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'conversion' => 'We could not get the exchange rate right now. Please try again shortly.',
            ]);
        }

        $this->recordHistory($request, $amount, $conversion);

        return view('converter.index', array_merge($this->viewData(), [
            'amount' => $amount,
            'sourceCurrency' => $sourceCurrency,
            'targetCurrency' => $targetCurrency,
            'conversion' => $conversion,
            'histories' => $this->recentHistory($request),
        ]));
    }

    public function clearHistory(Request $request): RedirectResponse
    {
        ConversionHistory::query()
            ->where('session_key', $this->historyKey($request))
            ->delete();

        return redirect()->route('converter.index')
            ->with('status', 'Conversion history cleared.');
    }

    /**
     * @param  array{base: string, quote: string, rate: float, date: string, convertedAmount: float}  $conversion
     */
    private function recordHistory(Request $request, float $amount, array $conversion): void
    {
        ConversionHistory::create([
            'session_key' => $this->historyKey($request),
            'amount' => $amount,
            'source_currency' => $conversion['base'],
            'target_currency' => $conversion['quote'],
            'rate' => $conversion['rate'],
            'converted_amount' => $conversion['convertedAmount'],
            'rate_date' => $conversion['date'],
        ]);
    }

    /**
     * @return Collection<int, ConversionHistory>
     */
    private function recentHistory(Request $request): Collection
    {
        return ConversionHistory::query()
            ->where('session_key', $this->historyKey($request))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    }

    private function historyKey(Request $request): string
    {
        $sessionKey = $request->session()->get('conversion_history_key');

        if (! is_string($sessionKey) || $sessionKey === '' || strlen($sessionKey) > 100) {
            $sessionKey = Str::random(40);
            $request->session()->put('conversion_history_key', $sessionKey);
        }

        return $sessionKey;
    }

    /**
     * @return array{currencies: array<string, string>, defaultSourceCurrency: string, defaultTargetCurrency: string}
     */
    private function viewData(): array
    {
        return [
            'currencies' => (array) config('currencies.available', []),
            'defaultSourceCurrency' => (string) config('currencies.defaults.from'),
            'defaultTargetCurrency' => (string) config('currencies.defaults.to'),
        ];
    }
}
