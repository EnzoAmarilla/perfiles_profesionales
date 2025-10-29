<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'value',
        'comment',
        'answer',
        'published',
        'user_id',
        'title',
        'proyect_type',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}