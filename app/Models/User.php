<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'document_type',
        'document_number',
        'birth_date',
        'nationality',
        'country_phone',
        'area_code',
        'phone_number',
        'profile_picture',
        'description',
        'user_type_id',
        'locality_id',
        'address',
        'street',
        'street_number',
        'floor',
        'apartment',
        'zone',
        'is_active',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Métodos requeridos por JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
