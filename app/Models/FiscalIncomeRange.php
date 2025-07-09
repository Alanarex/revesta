<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalIncomeRange extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'min',
        'max',
        'year',
        'description',
    ];

    public static function getFilterableAttributes(): array
    {
        return [
            'range' => 'Plage',
        ];
    }

    public static function getFilterableAttributeTypes(): array
    {
        return [
            'range' => 'select',
        ];
    }

    public static function optionsForSelect(): array
    {
        $latestYear = self::max('year');

        return self::where('year', $latestYear)
            ->get()
            ->mapWithKeys(function ($item) {
                $label = self::formatRange($item->min, $item->max);
                return [$item->id => $label];
            })
            ->toArray();
    }

    protected static function formatRange(?float $min, ?float $max): string
    {
        if (!is_null($min) && !is_null($max)) {
            return "{$min} < x < {$max}";
        } elseif (!is_null($min)) {
            return "{$min} <";
        } elseif (!is_null($max)) {
            return "{$max} >";
        } else {
            return 'Non défini';
        }
    }

}
