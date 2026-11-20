# Currency Converter

A responsive Laravel application for converting amounts between 36 curated currencies using daily reference rates from the [Frankfurter API](https://frankfurter.dev/). It includes input validation, an accessible currency-swap control, and a recent-conversion history scoped to each browser session.

Repository: https://github.com/krmad/laravel-currency-converter

## Features

- Convert between 36 currencies configured in `config/currencies.php`.
- Start with USD to ETB as the default currency pair.
- Swap the selected source and target currencies with an accessible button.
- Validate amounts and currency codes, with clear messages for invalid input.
- Accept zero amounts and format successful results to two decimal places.
- Display the exchange rate and its effective date with each successful conversion.
- Save successful conversions and show the latest 10 first.
- Keep history scoped to the current browser session and provide a clear-history action.
- Skip the rate API request when converting a currency to itself.
- Use a responsive Blade and Tailwind CSS interface with dark-mode styling.

## Technology stack

- PHP 8.3 or later (Composer requirement: `^8.3`)
- Laravel 13
- SQLite by default
- Blade, Tailwind CSS 4, and Vite 8
- PHPUnit 12
- Frankfurter API v2

## Requirements

Install PHP and Composer, plus Node.js and npm for building the frontend assets. The application uses SQLite by default, so no separate database server is required.

## Installation

```bash
git clone https://github.com/krmad/laravel-currency-converter.git
cd laravel-currency-converter
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Create the SQLite file if it does not already exist, then run the migrations:

```bash
touch database/database.sqlite
php artisan migrate
```

Build the frontend assets and start the development server:

```bash
npm run build
php artisan serve
```

Open <http://127.0.0.1:8000/converter> in your browser. While developing frontend changes, run `npm run dev` in a separate terminal instead of rebuilding each time.

## Running tests

Run the complete test suite:

```bash
php artisan test
```

Or run the converter and history feature tests individually:

```bash
php artisan test tests/Feature/CurrencyConverterTest.php
php artisan test tests/Feature/ConversionHistoryTest.php
```

The tests use an in-memory SQLite database and fake HTTP responses for the exchange-rate API, so they do not require a populated local database or live API access.

## Routes

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/converter` | Show the converter and current session's recent history |
| `POST` | `/convert` | Validate input and perform a conversion |
| `DELETE` | `/converter/history` | Clear the current session's conversion history |

The existing `/`, `/about`, `/contact`, and `/profile` pages are also available.

## Project structure

```text
app/
├── Http/
│   ├── Controllers/CurrencyConverterController.php
│   └── Requests/ConvertCurrencyRequest.php
├── Models/ConversionHistory.php
└── Services/FrankfurterService.php
config/
├── currencies.php
└── services.php
database/
├── factories/ConversionHistoryFactory.php
└── migrations/
resources/
├── css/app.css
├── js/app.js
└── views/
    └── converter/index.blade.php
routes/web.php
tests/Feature/
├── ConversionHistoryTest.php
└── CurrencyConverterTest.php
```

## Exchange-rate API and history notes

The app requests rates from the Frankfurter v2 endpoint:

```text
https://api.frankfurter.dev/v2/rate/{from}/{to}
```

No API key is required. Frankfurter provides daily reference exchange rates, not live market quotes. The application displays the rate date returned by the service. Only currencies listed in `config/currencies.php` can be selected. If the service is unavailable or returns invalid data, the app displays an error and does not save the failed conversion.

History is associated with a hidden server-side session key; it is not shown to other browser sessions and there are no user accounts. Clearing history deletes the records for the current session. Starting a new browser session (for example, after clearing cookies) does not give that session access to the prior session's history.
