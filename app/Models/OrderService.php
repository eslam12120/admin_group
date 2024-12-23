<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function service_special()
    {
        return $this->belongsTo(ServiceSpecial::class); // Assuming the foreign key is user_id in orders table
    }

}
