<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupouns', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('code')->unique(); // Unique coupon code
            $table->double('discount_value')->default(0); // Discount value
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed'); // Discount type; // Maximum number of uses for this coupon
            $table->date('start_date')->nullable(); // Start date of the coupon validity
            $table->date('end_date')->nullable(); // End date of the coupon validity
            $table->integer('is_active')->default('1'); // Status of the coupon (active/inactive)
            $table->timestamps(); // Created at & Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupouns');
    }
};
