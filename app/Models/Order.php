<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo(User::class); // Assuming the foreign key is user_id in orders table
    }

    /**
     * العلاقة مع القسيمة (Coupon)
     */
    public function coupon()
    {
        return $this->belongsTo(coupoun::class); // Assuming the foreign key is coupon_id in orders table
    }
}
