<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Convert currencies using public reference exchange rates from Frankfurter.">

    <title>Currency Converter - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.18),_transparent_45%),radial-gradient(circle_at_bottom_right,_rgba(99,102,241,0.20),_transparent_45%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.12),_transparent_45%),radial-gradient(circle_at_bottom_right,_rgba(99,102,241,0.16),_transparent_45%)]">
        <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-5 py-8 sm:px-8 lg:py-12">
            <header class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 rounded-xl text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-sky-600 text-white shadow-lg shadow-sky-600/25">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="size-6" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m0 0 3.5-3.5M12 18l-3.5-3.5M4.5 9.5A7.5 7.5 0 0 1 19.5 9.5M4.5 14.5A7.5 7.5 0 0 0 19.5 14.5"/>
                        </svg>
                    </span>
                    Currency Converter
                </a>

            </header>

            @if ($errors->has('conversion'))
                <div class="mt-8 flex gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 size-5 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.6 17.2A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.8L13.7 3.9a2 2 0 0 0-3.4 0Z"/>
                    </svg>
                    <p>{{ $errors->first('conversion') }}</p>
                </div>
            @endif

            @if (session('status'))
                <div class="mt-8 flex gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200" role="status">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="mt-0.5 size-5 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 9 17.25l10.5-10.5"/>
                    </svg>
                    <p>{{ session('status') }}</p>
                </div>
            @endif

            <main class="grid flex-1 items-center gap-8 py-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-12">
                <section class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-2xl shadow-slate-900/5 backdrop-blur-xl sm:p-9 dark:border-white/10 dark:bg-slate-900/70">
                    <div class="flex flex-col gap-3">
                        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold tracking-wide text-sky-700 uppercase dark:bg-sky-400/10 dark:text-sky-300">
                            <span class="size-1.5 rounded-full bg-sky-500"></span>
                            Frankfurter v2 rates
                        </span>
                        <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl dark:text-white">
                            Convert money with confidence.
                        </h1>
                        <p class="max-w-xl text-sm leading-7 text-slate-600 sm:text-base dark:text-slate-300">
                            Enter an amount, choose two currencies, and see the converted value plus the exchange rate used. USD to ETB is ready by default.
                        </p>
                    </div>

                    @php
                        $selectedAmount = old('amount', $amount ?? '');
                        $selectedSource = old('from', $sourceCurrency ?? $defaultSourceCurrency);
                        $selectedTarget = old('to', $targetCurrency ?? $defaultTargetCurrency);
                    @endphp

                    <form method="POST" action="{{ route('converter.convert') }}" class="mt-8 flex flex-col gap-6">
                        @csrf

                        <div class="flex flex-col gap-2">
                            <label for="amount" class="text-sm font-semibold text-slate-800 dark:text-slate-200">Amount</label>
                            <input
                                id="amount"
                                name="amount"
                                type="number"
                                step="any"
                                min="0"
                                inputmode="decimal"
                                autocomplete="off"
                                placeholder="e.g. 100"
                                value="{{ $selectedAmount }}"
                                aria-invalid="@if ($errors->has('amount')) true @else false @endif"
                                @class([
                                    'w-full rounded-xl border bg-white px-4 py-3 text-base text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:ring-4 dark:bg-slate-950 dark:text-white dark:placeholder:text-slate-500',
                                    'border-rose-400 focus:border-rose-500 focus:ring-rose-500/15' => $errors->has('amount'),
                                    'border-slate-200 focus:border-sky-500 focus:ring-sky-500/15 dark:border-slate-700' => ! $errors->has('amount'),
                                ])
                            >
                            @error('amount')
                                <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-5 sm:grid-cols-[1fr_auto_1fr] sm:items-end">
                            <div class="flex flex-col gap-2">
                                <label for="from" class="text-sm font-semibold text-slate-800 dark:text-slate-200">From</label>
                                <select
                                    id="from"
                                    name="from"
                                    aria-invalid="@if ($errors->has('from')) true @else false @endif"
                                    @class([
                                        'w-full rounded-xl border bg-white px-4 py-3 text-base text-slate-900 shadow-sm outline-none transition focus:ring-4 dark:bg-slate-950 dark:text-white',
                                        'border-rose-400 focus:border-rose-500 focus:ring-rose-500/15' => $errors->has('from'),
                                        'border-slate-200 focus:border-sky-500 focus:ring-sky-500/15 dark:border-slate-700' => ! $errors->has('from'),
                                    ])
                                >
                                    @foreach ($currencies as $code => $name)
                                        <option value="{{ $code }}" @selected($selectedSource === $code)>{{ $code }} - {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('from')
                                    <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="button"
                                id="swap-currencies"
                                aria-label="Swap source and target currencies"
                                aria-controls="from to"
                                title="Swap currencies"
                                class="flex size-11 cursor-pointer items-center justify-center justify-self-start rounded-full border border-slate-200 bg-slate-50 text-slate-500 transition hover:bg-white hover:text-slate-800 focus:outline-none focus:ring-4 focus:ring-sky-500/20 sm:mb-1 sm:justify-self-center dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h13m0 0-3.5-3.5M20 7l-3.5 3.5M17 17H4m0 0 3.5 3.5M4 17l3.5-3.5"/>
                                </svg>
                            </button>

                            <div class="flex flex-col gap-2">
                                <label for="to" class="text-sm font-semibold text-slate-800 dark:text-slate-200">To</label>
                                <select
                                    id="to"
                                    name="to"
                                    aria-invalid="@if ($errors->has('to')) true @else false @endif"
                                    @class([
                                        'w-full rounded-xl border bg-white px-4 py-3 text-base text-slate-900 shadow-sm outline-none transition focus:ring-4 dark:bg-slate-950 dark:text-white',
                                        'border-rose-400 focus:border-rose-500 focus:ring-rose-500/15' => $errors->has('to'),
                                        'border-slate-200 focus:border-sky-500 focus:ring-sky-500/15 dark:border-slate-700' => ! $errors->has('to'),
                                    ])
                                >
                                    @foreach ($currencies as $code => $name)
                                        <option value="{{ $code }}" @selected($selectedTarget === $code)>{{ $code }} - {{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('to')
                                    <p class="text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <span id="swap-status" class="sr-only" aria-live="polite"></span>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/25 transition hover:bg-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-500/25 sm:w-auto">
                            Convert currency
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-5-5m5 5-5 5"/>
                            </svg>
                        </button>
                    </form>
                </section>

                <section class="relative overflow-hidden rounded-3xl bg-slate-900 p-6 text-white shadow-2xl shadow-slate-900/20 sm:p-9 dark:bg-slate-800/80 dark:ring-1 dark:ring-white/10" aria-live="polite">
                    <div class="absolute -top-24 -right-24 size-64 rounded-full bg-sky-500/20 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-20 size-72 rounded-full bg-indigo-500/20 blur-3xl"></div>

                    <div class="relative flex flex-col gap-8">
                        <div>
                            <p class="text-xs font-semibold tracking-[0.2em] text-sky-300 uppercase">Conversion result</p>
                            @if (isset($conversion))
                                <p class="mt-5 text-4xl font-bold tracking-tight sm:text-5xl">
                                    {{ number_format((float) $conversion['convertedAmount'], 2) }}
                                    <span class="text-2xl font-semibold text-sky-300 sm:text-3xl">{{ $conversion['quote'] }}</span>
                                </p>
                                <p class="mt-3 text-sm text-slate-300">
                                    From {{ number_format((float) $amount, 2) }} {{ $conversion['base'] }}
                                </p>
                            @else
                                <p class="mt-5 text-3xl font-semibold tracking-tight text-slate-400 sm:text-4xl">
                                    Ready when you are.
                                </p>
                                <p class="mt-3 text-sm leading-7 text-slate-300">
                                    Your converted amount and exchange rate will appear here after you submit the form.
                                </p>
                            @endif
                        </div>

                        @if (isset($conversion))
                            <dl class="grid gap-3 rounded-2xl border border-white/10 bg-white/5 p-5 text-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-300">Exchange rate</dt>
                                    <dd class="font-semibold">1 {{ $conversion['base'] }} = {{ number_format((float) $conversion['rate'], 4) }} {{ $conversion['quote'] }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4 border-t border-white/10 pt-3">
                                    <dt class="text-slate-300">Rate date</dt>
                                    <dd class="font-semibold">{{ $conversion['date'] }}</dd>
                                </div>
                            </dl>
                        @endif

                        <p class="text-xs leading-6 text-slate-400">
                            Rates are daily reference rates from Frankfurter, not live market quotes.
                        </p>
                    </div>
                </section>
            </main>

            <section class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-2xl shadow-slate-900/5 backdrop-blur-xl sm:p-9 dark:border-white/10 dark:bg-slate-900/70" aria-labelledby="conversion-history-heading">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 id="conversion-history-heading" class="text-xl font-bold tracking-tight text-slate-950 sm:text-2xl dark:text-white">
                            Recent conversions
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                            Your latest 10 conversions in this browser session.
                        </p>
                    </div>

                    @if ($histories->isNotEmpty())
                        <form method="POST" action="{{ route('converter.history.clear') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-500/20 sm:w-auto dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200 dark:hover:bg-rose-950/70">
                                Clear history
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5h6v2m-8 0 .7 12h8.6L17 7M10 11v5m4-5v5"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>

                @if ($histories->isEmpty())
                    <p class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 p-6 text-sm leading-7 text-slate-600 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-300">
                        No conversions yet. Your successful conversions will appear here.
                    </p>
                @else
                    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full min-w-[720px] border-collapse text-left text-sm">
                            <thead class="bg-slate-100/80 text-xs font-semibold tracking-wide text-slate-600 uppercase dark:bg-slate-800/80 dark:text-slate-300">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Amount</th>
                                    <th scope="col" class="px-4 py-3">From</th>
                                    <th scope="col" class="px-4 py-3">To</th>
                                    <th scope="col" class="px-4 py-3">Exchange rate</th>
                                    <th scope="col" class="px-4 py-3">Converted amount</th>
                                    <th scope="col" class="px-4 py-3">Rate date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach ($histories as $history)
                                    <tr class="bg-white/70 dark:bg-slate-900/40">
                                        <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">{{ number_format((float) $history->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $history->source_currency }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $history->target_currency }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">1 {{ $history->source_currency }} = {{ number_format((float) $history->rate, 4) }} {{ $history->target_currency }}</td>
                                        <td class="px-4 py-3 font-semibold text-sky-700 dark:text-sky-300">{{ number_format((float) $history->converted_amount, 2) }} {{ $history->target_currency }}</td>
                                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $history->rate_date?->toDateString() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</body>
</html>
