<?php

namespace Database\Factories;

use App\Models\ConversionHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ConversionHistory>
 */
class ConversionHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currencies = array_keys((array) config('currencies.available', []));

        return [
            'session_key' => Str::random(40),
            'amount' => fake()->randomFloat(2, 1, 1000),
            'source_currency' => fake()->randomElement($currencies),
            'target_currency' => fake()->randomElement($currencies),
            'rate' => fake()->randomFloat(4, 0.5, 200),
            'converted_amount' => fake()->randomFloat(2, 1, 10000),
            'rate_date' => now()->toDateString(),
        ];
    }

    /**
     * Indicate that the history belongs to the given browser session.
     */
    public function forSession(string $sessionKey): static
    {
        return $this->state(fn (array $attributes) => [
            'session_key' => $sessionKey,
        ]);
    }
}
