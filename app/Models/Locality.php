<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    protected $fillable = [
        'name',
        'short_code',
        'disabled',
        'province_id',
        'state_id',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function zipCodes()
    {
        return $this->hasMany(ZipCode::class);
    }
}