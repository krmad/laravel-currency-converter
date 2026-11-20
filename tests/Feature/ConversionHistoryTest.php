<?php

namespace Tests\Feature;

use App\Models\ConversionHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConversionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_conversion_is_saved_to_history(): void
    {
        $this->fakeSuccessfulConversion();

        $response = $this->withSession(['conversion_history_key' => 'visitor-a'])
            ->post(route('converter.convert'), [
                'amount' => '10',
                'from' => 'USD',
                'to' => 'ETB',
            ]);

        $response->assertOk()
            ->assertSee('Recent conversions')
            ->assertViewHas('histories', fn ($histories) => $histories->count() === 1);

        $this->assertDatabaseHas('conversion_histories', [
            'session_key' => 'visitor-a',
            'amount' => 10,
            'source_currency' => 'USD',
            'target_currency' => 'ETB',
            'rate' => 162.13,
            'converted_amount' => 1621.3,
        ]);

        $this->assertSame('2026-11-19', ConversionHistory::query()->firstOrFail()->rate_date->toDateString());

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.frankfurter.dev/v2/rate/usd/etb');
    }

    public function test_failed_conversion_is_not_saved_to_history(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.frankfurter.dev/*' => Http::response(['message' => 'Service unavailable'], 500),
        ]);

        $this->withSession(['conversion_history_key' => 'visitor-a'])
            ->from(route('converter.index'))
            ->post(route('converter.convert'), [
                'amount' => '10',
                'from' => 'USD',
                'to' => 'ETB',
            ])
            ->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors('conversion');

        $this->assertDatabaseCount('conversion_histories', 0);
    }

    public function test_converter_displays_history_newest_first_with_clear_button(): void
    {
        $older = ConversionHistory::factory()->forSession('visitor-a')->create([
            'amount' => 1,
            'rate' => 1,
            'converted_amount' => 111.11,
            'created_at' => now()->subDay(),
        ]);

        $newer = ConversionHistory::factory()->forSession('visitor-a')->create([
            'amount' => 2,
            'rate' => 2,
            'converted_amount' => 222.22,
            'created_at' => now(),
        ]);

        $this->withSession(['conversion_history_key' => 'visitor-a'])
            ->get(route('converter.index'))
            ->assertOk()
            ->assertSee('Recent conversions')
            ->assertSee('Clear history')
            ->assertSeeInOrder([
                number_format((float) $newer->converted_amount, 2),
                number_format((float) $older->converted_amount, 2),
            ])
            ->assertViewHas('histories', fn ($histories) => $histories->pluck('id')->all() === [$newer->id, $older->id]);
    }

    public function test_converter_displays_only_latest_ten_conversions(): void
    {
        ConversionHistory::factory()->count(12)->forSession('visitor-a')->create();

        $this->withSession(['conversion_history_key' => 'visitor-a'])
            ->get(route('converter.index'))
            ->assertOk()
            ->assertViewHas('histories', fn ($histories) => $histories->count() === 10);

        $this->assertDatabaseCount('conversion_histories', 12);
    }

    public function test_history_is_private_to_each_browser_session(): void
    {
        $mine = ConversionHistory::factory()->forSession('visitor-a')->create([
            'amount' => 123.45,
            'rate' => 1,
            'converted_amount' => 123.45,
        ]);

        $theirs = ConversionHistory::factory()->forSession('visitor-b')->create([
            'amount' => 987.65,
            'rate' => 1,
            'converted_amount' => 987.65,
        ]);

        $this->withSession(['conversion_history_key' => 'visitor-a'])
            ->get(route('converter.index'))
            ->assertViewHas('histories', fn ($histories) => $histories->pluck('id')->all() === [$mine->id])
            ->assertSee('123.45')
            ->assertDontSee('987.65');

        $this->withSession(['conversion_history_key' => 'visitor-b'])
            ->get(route('converter.index'))
            ->assertViewHas('histories', fn ($histories) => $histories->pluck('id')->all() === [$theirs->id])
            ->assertSee('987.65')
            ->assertDontSee('123.45');
    }

    public function test_clear_history_removes_only_current_browser_sessions_history(): void
    {
        $mine = ConversionHistory::factory()->forSession('visitor-a')->count(2)->create();
        $theirs = ConversionHistory::factory()->forSession('visitor-b')->create();

        $this->withSession(['conversion_history_key' => 'visitor-a'])
            ->from(route('converter.index'))
            ->delete(route('converter.history.clear'))
            ->assertRedirect(route('converter.index'))
            ->assertSessionHas('status', 'Conversion history cleared.');

        foreach ($mine as $history) {
            $this->assertDatabaseMissing('conversion_histories', ['id' => $history->id]);
        }

        $this->assertDatabaseHas('conversion_histories', ['id' => $theirs->id]);
        $this->assertDatabaseCount('conversion_histories', 1);
    }

    private function fakeSuccessfulConversion(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.frankfurter.dev/v2/rate/usd/etb' => Http::response([
                'date' => '2026-11-19',
                'base' => 'USD',
                'quote' => 'ETB',
                'rate' => 162.13,
            ]),
        ]);
    }
}
