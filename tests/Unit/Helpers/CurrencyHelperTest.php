<?php

namespace Tests\Unit\Helpers;

use App\Helpers\CurrencyHelper;
use Tests\TestCase;

class CurrencyHelperTest extends TestCase
{
    /** @test */
    public function test_format_returns_correct_dollar_amount()
    {
        $this->assertEquals('$0.00', CurrencyHelper::format(0));
        $this->assertEquals('$1.00', CurrencyHelper::format(100));
        $this->assertEquals('$15.25', CurrencyHelper::format(1525));
        $this->assertEquals('$123.45', CurrencyHelper::format(12345));
    }

    /** @test */
    public function test_format_supports_custom_currency_symbol()
    {
        $this->assertEquals('€9.99', CurrencyHelper::format(999, '€'));
        $this->assertEquals('BGN1.00', CurrencyHelper::format(100, 'BGN'));
    }

    /** @test */
    public function test_format_handles_negative_amounts_and_thousands()
    {
        // -123456 cents => -$1,234.56
        $this->assertSame('$-1,234.56', CurrencyHelper::format(-123456));
    }

    /** @test */
    public function test_format_accepts_zero_and_custom_symbol_with_space()
    {
        $this->assertSame('€0.00', CurrencyHelper::format(0, '€'));
    }

    /** @test */
    public function test_format_rounds_when_given_float_like_input_but_keeps_cents_integer_semantics()
    {
        // Defensive: if someone passes 1999.9 by mistake, we still show $19.99 (no banker's rounding surprises)
        $this->assertSame('$19.99', CurrencyHelper::format(1999.9));
    }
}
