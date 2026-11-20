# Laravel Learning

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
