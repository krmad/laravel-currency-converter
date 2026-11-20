<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConvertCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $amount = $this->input('amount');

        if (is_string($amount)) {
            $this->merge(['amount' => trim($amount)]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencyCodes = array_keys((array) config('currencies.available', []));

        return [
            'amount' => ['required', 'numeric', 'gte:0', 'max:1000000000000'],
            'from' => ['required', 'string', Rule::in($currencyCodes)],
            'to' => ['required', 'string', Rule::in($currencyCodes)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Please enter an amount.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.gte' => 'The amount cannot be negative.',
            'amount.max' => 'The amount is too large.',
            'from.required' => 'Please choose a source currency.',
            'from.string' => 'Please choose a valid source currency.',
            'from.in' => 'Please choose a valid source currency.',
            'to.required' => 'Please choose a target currency.',
            'to.string' => 'Please choose a valid target currency.',
            'to.in' => 'Please choose a valid target currency.',
        ];
    }
}
