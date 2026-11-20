# Currency Converter

<<<<<<< HEAD
A clean, responsive Laravel currency converter that uses public reference exchange rates from the Frankfurter API. The app supports multiple currencies, a one-click currency swap, clear amount validation, and a private conversion history for each browser session.

Repository: https://github.com/krmad/laravel-currency-converter

## Features

- Currency conversion using current reference rates from the Frankfurter API.
- Multiple currency selections from a curated list in `config/currencies.php`.
- Currency swap with an accessible button that swaps only the two selected currencies.
- Amount validation with friendly error messages for empty, invalid, and negative amounts.
- Zero amount support, including successful conversion of `0` to `0.00`.
- Exchange rate and rate date displayed after every successful conversion.
- Conversion history saved automatically after every successful conversion.
- Latest 10 conversions displayed below the converter, newest first.
- Clear conversion history button for the current browser session only.
- Browser-session history privacy using a hidden server-side session key.
- Responsive interface with Tailwind CSS, including dark-mode styles.

## Technologies Used

- Laravel 13
- PHP 8.4
- SQLite
- Blade
- Tailwind CSS
- JavaScript
- Frankfurter API

## Installation on macOS

This guide assumes PHP 8.4, Composer, and Node.js/npm are already installed.

### 1. Clone the repository

```bash
git clone https://github.com/krmad/laravel-currency-converter.git
cd laravel-currency-converter
```

### 2. Install Composer dependencies

```bash
composer install
```

This installs the Laravel framework and PHP packages listed in `composer.lock`.

### 3. Install npm dependencies

```bash
npm install
```

This installs Vite and Tailwind CSS so the frontend assets can be built.

### 4. Configure the environment file

```bash
cp .env.example .env
```

The `.env` file is local to your machine and should not be committed. The example file already sets `APP_NAME="Currency Converter"` and SQLite as the database connection.

### 5. Generate the application key when needed

```bash
php artisan key:generate
```

Run this after copying `.env.example`. It adds an application key used by Laravel for encryption and sessions.

## Database Setup

This project uses SQLite, which is already configured in `config/database.php`.

Create the SQLite database file if it does not exist:

```bash
touch database/database.sqlite
```

Run the migrations:

```bash
php artisan migrate
```

This creates the standard Laravel tables plus the `conversion_histories` table used by the Conversion History feature.

## Running the Application

Build the production frontend assets:

```bash
npm run build
```

Start the Laravel development server:

```bash
php artisan serve
```

Then open the converter in your browser:

```text
http://127.0.0.1:8000/converter
```

If you are actively changing CSS or JavaScript, you can run `npm run dev` in a second terminal instead of rebuilding after every change.

## Running Automated Tests

Run the full test suite:

```bash
php artisan test
```

Run only the conversion history tests:

```bash
php artisan test tests/Feature/ConversionHistoryTest.php
```

Tests use an in-memory SQLite database configured in `phpunit.xml`, so they do not depend on data in your local `database/database.sqlite` file.

## Project Structure Overview

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── CurrencyConverterController.php   # Page, conversion, and history actions
│   └── Requests/
│       └── ConvertCurrencyRequest.php         # Amount and currency validation
├── Models/
│   └── ConversionHistory.php                  # Eloquent model for saved conversions
└── Services/
    └── FrankfurterService.php                 # Exchange-rate API integration

config/
├── currencies.php                             # Available currencies and defaults
└── services.php                               # Frankfurter API configuration

database/
├── factories/
│   └── ConversionHistoryFactory.php           # Test data factory
├── migrations/
│   └── 2026_11_20_002019_create_conversion_histories_table.php
└── database.sqlite                            # Local SQLite database

resources/
├── css/app.css                                # Tailwind entry point
├── js/app.js                                  # Currency swap interaction
└── views/
    ├── converter/index.blade.php              # Converter and conversion history UI
    ├── welcome.blade.php
    ├── about.blade.php
    ├── contact.blade.php
    └── profile.blade.php

routes/
└── web.php                                    # Application routes

tests/Feature/
├── ConversionHistoryTest.php                  # Saving, display, privacy, and clearing tests
└── CurrencyConverterTest.php                  # Conversion, validation, swap, and UI tests
```

### Main routes

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/converter` | Show the converter and recent history |
| `POST` | `/convert` | Validate and perform a conversion |
| `DELETE` | `/converter/history` | Clear the current session's history |

The original `/`, `/about`, `/contact`, and `/profile` routes remain available.

## API Information and Limitations

The app uses the Frankfurter API v2 rate endpoint:

```text
https://api.frankfurter.dev/v2/rate/{from}/{to}
```

- No API key is required.
- The app displays the exchange rate and the rate date returned by the API.
- Converting a currency to itself skips the API call and uses a rate of `1`.
- If the API is unavailable or returns invalid data, the app shows a friendly error and does not save a history row.
- Frankfurter provides daily reference rates, not live market quotes.
- Only currencies listed in `config/currencies.php` can be selected in the UI.
- Conversion history is private to the current browser session. There are no user accounts, and clearing browser cookies starts a new session with no access to the previous session's history.

## License

No license has been specified for this project. Do not assume the project is MIT licensed or otherwise licensed. A license should only be added if the project owner chooses one.
=======
A Laravel learning project with a currency conversion interface. It supports converting between a curated list of currencies through the Frankfurter API and keeps the ten most recent conversions in a browser session.

## Features

- Convert amounts across 36 configured currencies
- Use USD to ETB as the default currency pair
- Validate amounts and currency codes before conversion
- Fetch live exchange rates from the Frankfurter API
- Bypass the API for same-currency conversions
- Show conversion history for the current browser session
- Clear conversion history
- Display clear validation and service error messages

## Stack

- **Runtime:** PHP 8.3+
- **Framework:** Laravel 13
- **Frontend:** Blade, Tailwind CSS 4, and Vite 8
- **Database:** SQLite by default
- **Testing:** PHPUnit 12
- **Exchange-rate provider:** [Frankfurter API](https://frankfurter.dev/)

## Project Structure

```text
app/
  Http/
    Controllers/CurrencyConverterController.php  Conversion and history actions
    Requests/ConvertCurrencyRequest.php          Conversion input validation
  Models/ConversionHistory.php                   Conversion history Eloquent model
  Services/FrankfurterService.php                Frankfurter API integration
config/
  currencies.php                                  Available currencies and defaults
  services.php                                    Frankfurter API settings
resources/views/
  converter/index.blade.php                       Converter interface
routes/web.php                                    Application routes
database/migrations/                              Database schema
 tests/Feature/                                   Converter and history feature tests# Laravel Learning

A Laravel learning project with a currency conversion interface. It supports converting between a curated list of currencies through the Frankfurter API and keeps the ten most recent conversions in a browser session.

## Features

- Convert amounts across 36 configured currencies
- Use USD to ETB as the default currency pair
- Validate amounts and currency codes before conversion
- Fetch live exchange rates from the Frankfurter API
- Bypass the API for same-currency conversions
- Show conversion history for the current browser session
- Clear conversion history
- Display clear validation and service error messages

## Stack

- **Runtime:** PHP 8.3+
- **Framework:** Laravel 13
- **Frontend:** Blade, Tailwind CSS 4, and Vite 8
- **Database:** SQLite by default
- **Testing:** PHPUnit 12
- **Exchange-rate provider:** [Frankfurter API](https://frankfurter.dev/)

## Project Structure

```text
app/
  Http/
    Controllers/CurrencyConverterController.php  Conversion and history actions
    Requests/ConvertCurrencyRequest.php          Conversion input validation
  Models/ConversionHistory.php                   Conversion history Eloquent model
  Services/FrankfurterService.php                Frankfurter API integration
config/
  currencies.php                                  Available currencies and defaults
  services.php                                    Frankfurter API settings
resources/views/
  converter/index.blade.php                       Converter interface
routes/web.php                                    Application routes
database/migrations/                              Database schema
 tests/Feature/                                   Converter and history feature tests
>>>>>>> origin/main
