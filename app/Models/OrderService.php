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
    public function specialist()
    {
        return $this->belongsTo(Specialist::class); // Assuming the foreign key is user_id in orders table
    }
    public function orderfiles()
    {
        return $this->hasMany(OrderFile::class, 'order_id')
            ->where('type', 'services'); // Filtering by type 'services' // Assuming the foreign key is user_id in orders table
    }
    public function user()
    {
        return $this->belongsTo(User::class); // Assuming the foreign key is user_id in orders table
    }
    public function coupon()
    {
        return $this->belongsTo(coupoun::class); // Assuming the foreign key is coupon_id in orders table
    }
}
