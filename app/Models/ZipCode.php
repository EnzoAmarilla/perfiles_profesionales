<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'locality_id'];

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }
}