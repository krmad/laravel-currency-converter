<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertCurrencyRequest;
use App\Services\FrankfurterService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CurrencyConverterController extends Controller
{
    public function __construct(private readonly FrankfurterService $frankfurterService) {}

    public function index(): View
    {
        return view('converter.index', $this->viewData());
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

        return view('converter.index', array_merge($this->viewData(), [
            'amount' => $amount,
            'sourceCurrency' => $sourceCurrency,
            'targetCurrency' => $targetCurrency,
            'conversion' => $conversion,
        ]));
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
