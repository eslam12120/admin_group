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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable()->index();
        //    $table->bigInteger('merchant_id')->nullable()->index();
            $table->bigInteger('order_id')->nullable()->index();
            $table->text('invoice_id')->nullable()->index();
            $table->text('message')->nullable();
            $table->text('url')->nullable();
            $table->string('status')->nullable();
            $table->string('kind')->nullable();
            $table->string('card_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
