<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderNormalSpecialist extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function specialist()
    {
        return $this->belongsTo(Specialist::class); // Assuming the foreign key is user_id in orders table
    }
}
