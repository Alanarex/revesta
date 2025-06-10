<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'url',
        'housing_id',
    ];

    public function housing()
    {
        return $this->belongsTo(Housing::class);
    }
}
