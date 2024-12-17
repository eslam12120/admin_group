<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function rate()
    {
        return $this->hasMany(Rate::class);
    }
    public function special()
    {
        return $this->hasMany(SpecialistSpecial::class,);
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
