<?php

namespace App\Models;

use Database\Factories\ConversionHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'session_key',
    'amount',
    'source_currency',
    'target_currency',
    'rate',
    'converted_amount',
    'rate_date',
])]
class ConversionHistory extends Model
{
    /** @use HasFactory<ConversionHistoryFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'rate' => 'float',
            'converted_amount' => 'float',
            'rate_date' => 'date',
        ];
    }
}
