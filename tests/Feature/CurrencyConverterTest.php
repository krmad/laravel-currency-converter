<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    use RefreshDatabase;

    public function test_converter_page_renders_default_currencies(): void
    {
        $response = $this->get(route('converter.index'));

        $response->assertOk()
            ->assertSee('Currency Converter')
            ->assertViewHas('defaultSourceCurrency', 'USD')
            ->assertViewHas('defaultTargetCurrency', 'ETB')
            ->assertViewHas('currencies');
    }

    public function test_converter_page_has_accessible_swap_button(): void
    {
        $response = $this->get(route('converter.index'));

        $response->assertOk()
            ->assertSee('id="from"', false)
            ->assertSee('id="to"', false)
            ->assertSee('id="swap-currencies"', false)
            ->assertSee('type="button"', false)
            ->assertSee('aria-label="Swap source and target currencies"', false)
            ->assertSee('aria-controls="from to"', false)
            ->assertSee('id="swap-status"', false);
    }

    public function test_converter_interface_does_not_show_site_navigation_links(): void
    {
        $this->get(route('converter.index'))
            ->assertOk()
            ->assertDontSee('Home')
            ->assertDontSee('About')
            ->assertDontSee('Contact')
            ->assertDontSee('Profile');
    }

    public function test_convert_rejects_empty_submission_with_clear_messages(): void
    {
        Http::preventStrayRequests();

        $response = $this->from(route('converter.index'))->post(route('converter.convert'), [
            'amount' => '',
            'from' => '',
            'to' => '',
        ]);

        $response->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors([
                'amount' => 'Please enter an amount.',
                'from' => 'Please choose a source currency.',
                'to' => 'Please choose a target currency.',
            ])
            ->assertSessionHasInput('amount', '');
    }

    public function test_convert_rejects_whitespace_only_amount_and_preserves_input(): void
    {
        Http::preventStrayRequests();

        $response = $this->from(route('converter.index'))->post(route('converter.convert'), [
            'amount' => '   ',
            'from' => 'USD',
            'to' => 'ETB',
        ]);

        $response->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors([
                'amount' => 'Please enter an amount.',
            ])
            ->assertSessionHasInput('amount', '')
            ->assertSessionHasInput('from', 'USD')
            ->assertSessionHasInput('to', 'ETB');
    }

    public function test_convert_allows_zero_amount(): void
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

        $response = $this->post(route('converter.convert'), [
            'amount' => '0',
            'from' => 'USD',
            'to' => 'ETB',
        ]);

        $response->assertOk()
            ->assertSee('0.00')
            ->assertViewHas('conversion.convertedAmount', 0.0);

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.frankfurter.dev/v2/rate/usd/etb');
    }

    public function test_convert_rejects_negative_amount_and_preserves_input(): void
    {
        Http::preventStrayRequests();

        $response = $this->from(route('converter.index'))->post(route('converter.convert'), [
            'amount' => '-1',
            'from' => 'USD',
            'to' => 'ETB',
        ]);

        $response->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors([
                'amount' => 'The amount cannot be negative.',
            ])
            ->assertSessionHasInput('amount', '-1')
            ->assertSessionHasInput('from', 'USD')
            ->assertSessionHasInput('to', 'ETB');
    }

    public function test_convert_rejects_unknown_currency(): void
    {
        Http::preventStrayRequests();

        $response = $this->post(route('converter.convert'), [
            'amount' => '10',
            'from' => 'XXX',
            'to' => 'ETB',
        ]);

        $response->assertRedirect()
            ->assertSessionHasErrors([
                'from' => 'Please choose a valid source currency.',
            ]);
    }

    public function test_convert_returns_converted_amount_and_rate(): void
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

        $response = $this->post(route('converter.convert'), [
            'amount' => '1',
            'from' => 'USD',
            'to' => 'ETB',
        ]);

        $response->assertOk()
            ->assertSee('162.13')
            ->assertSee('2026-11-19')
            ->assertViewHas('conversion', [
                'base' => 'USD',
                'quote' => 'ETB',
                'rate' => 162.13,
                'date' => '2026-11-19',
                'convertedAmount' => 162.13,
            ]);

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.frankfurter.dev/v2/rate/usd/etb');
    }

    public function test_convert_does_not_call_api_for_same_currency(): void
    {
        Http::preventStrayRequests();

        $response = $this->post(route('converter.convert'), [
            'amount' => '5',
            'from' => 'USD',
            'to' => 'USD',
        ]);

        $response->assertOk()
            ->assertViewHas('conversion.rate', 1.0)
            ->assertViewHas('conversion.convertedAmount', 5.0);

        Http::assertNothingSent();
    }

    public function test_convert_preserves_input_after_server_error(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.frankfurter.dev/*' => Http::response(['message' => 'Service unavailable'], 500),
        ]);

        $response = $this->from(route('converter.index'))->post(route('converter.convert'), [
            'amount' => '10',
            'from' => 'USD',
            'to' => 'ETB',
        ]);

        $response->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors([
                'conversion' => 'We could not get the exchange rate right now. Please try again shortly.',
            ])
            ->assertSessionHasInput('amount', '10')
            ->assertSessionHasInput('from', 'USD')
            ->assertSessionHasInput('to', 'ETB');
    }

    public function test_convert_preserves_input_after_connection_failure(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.frankfurter.dev/*' => Http::failedConnection(),
        ]);

        $response = $this->from(route('converter.index'))->post(route('converter.convert'), [
            'amount' => '25',
            'from' => 'EUR',
            'to' => 'ETB',
        ]);

        $response->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors('conversion')
            ->assertSessionHasInput('amount', '25');
    }

    public function test_convert_preserves_input_after_invalid_rate_payload(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'https://api.frankfurter.dev/*' => Http::response([
                'date' => '2026-11-19',
                'base' => 'USD',
                'quote' => 'ETB',
            ]),
        ]);

        $response = $this->from(route('converter.index'))->post(route('converter.convert'), [
            'amount' => '12',
            'from' => 'USD',
            'to' => 'ETB',
        ]);

        $response->assertRedirect(route('converter.index'))
            ->assertSessionHasErrors('conversion')
            ->assertSessionHasInput('amount', '12');
    }
}
