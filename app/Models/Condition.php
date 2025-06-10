<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condition extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'aid_id',
        'model',
        'attribute',
        'condition_type',
        'operator',
        'value',
        'fiscal_income_range_id',
    ];

    public function aid()
    {
        return $this->belongsTo(Aid::class);
    }

    public function fiscalIncomeRange()
    {
        return $this->belongsTo(FiscalIncomeRange::class);
    }
}
