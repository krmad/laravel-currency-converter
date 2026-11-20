<?php

use App\Http\Controllers\CurrencyConverterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});
Route::get('/profile', function () {

    $name = 'Kirubel';
    $country = 'Ethiopia';

    return view('profile', [
        'name' => $name,
        'country' => $country,
    ]);

});

Route::get('/converter', [CurrencyConverterController::class, 'index'])->name('converter.index');
Route::post('/convert', [CurrencyConverterController::class, 'convert'])->name('converter.convert');
Route::delete('/converter/history', [CurrencyConverterController::class, 'clearHistory'])->name('converter.history.clear');
