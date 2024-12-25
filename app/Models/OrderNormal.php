<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderNormal extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class); // Assuming the foreign key is user_id in orders table
    }
    public function ordernormal()
    {
        return $this->hasMany(OrderNormalSpecialist::class, 'order_id'); // Assuming the foreign key is user_id in orders table
    }
}
