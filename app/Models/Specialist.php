<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Specialist extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guarded = [];
    protected $hidden = [
        'password',
        'is_verify',
    ];
    public function rate()
    {
        return $this->hasMany(Rate::class);
    }
    public function special()
    {
        return $this->hasMany(SpecialistSpecial::class,);
    }
    public function special_order()
    {
        // Use a closure to select specific columns dynamically
        return $this->hasMany(SpecialistSpecial::class)
            ->selectRaw('id, specialist_id, special_id,
                     CASE WHEN :lang = "ar" THEN job_name_ar ELSE job_name_en END as job_name,
                     created_at, updated_at')
            ->setBindings(['lang' => request('lang', 'ar')]); // Default to 'en' if no lang is provided
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
    public function government()
    {
        return $this->belongsTo(Government::class, 'gov_id');
    }
    public function orderNormalSpecialists()
    {
        return $this->hasMany(OrderNormalSpecialist::class, 'specialist_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function languages()
    {
        return $this->belongsToMany(Language::class, 'language_specialists');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificates::class);
    }

    public function skills()
    {
        return $this->hasMany(SkillSpecialist::class);
    }

    public function specializations()
    {
        return $this->hasMany(SpecialistSpecial::class);
    }
}
