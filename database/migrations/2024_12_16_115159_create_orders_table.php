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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('special_id')->nullable(); // Foreign key for special
            $table->unsignedBigInteger('specialist_id')->nullable(); // Foreign key for specialist
            $table->string('status')->default('pending'); // Status
            $table->string('type_payment')->nullable(); // Payment type
            $table->string('type_com')->nullable(); // Communication type
            $table->text('desc')->nullable(); // Description
            $table->text('address')->nullable(); // Address
            $table->double('price')->default(0); // Price with precision
            $table->double('paid_now')->default(0); // Paid now
            $table->integer('have_discount')->default(0); // Discount flag
            $table->string('code')->nullable(); // Discount code
            $table->double('value_of_discount')->default(0); // Discount value
            $table->unsignedBigInteger('user_id')->nullable(); // User ID (foreign key for the user)
            $table->unsignedBigInteger('coupoun_id')->nullable(); // Coupon ID
            $table->timestamps(); // Created at & Updated at
            // Add foreign key constraints
            $table->foreign('special_id')->references('id')->on('specials')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('specialists')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //  $table->foreign('coupoun_id')->references('id')->on('coupons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
