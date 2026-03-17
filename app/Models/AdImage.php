<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'url',
        'order',
    ];

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
