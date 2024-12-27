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
    public function orderfiles()
    {
        return $this->hasMany(OrderFile::class, 'order_id')
            ->where('type', 'normal'); // Filtering by type 'services' // Assuming the foreign key is user_id in orders table
    }
    
  
    /**
     * العلاقة مع القسيمة (Coupon)
     */
    public function coupon()
    {
        return $this->belongsTo(coupoun::class); // Assuming the foreign key is coupon_id in orders table
    }

}
