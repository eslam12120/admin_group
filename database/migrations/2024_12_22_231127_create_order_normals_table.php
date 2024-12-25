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
        Schema::create('order_normals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('special_id')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->string('type_payment')->nullable();
            $table->text('desc')->nullable();
            $table->text('address')->nullable();
            $table->double('price')->default(0)->nullable(); // Adjust precision as needed
            $table->double('paid_now')->default('0')->nullable();
            $table->boolean('have_discount')->default(0);
            $table->string('code')->nullable();
            $table->double('value_of_discount')->default(0)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('coupoun_id')->nullable();
            $table->string('audio_path')->nullable(); // File path for audio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_normals');
    }
};
