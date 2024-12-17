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
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->double('rate')->default('0')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->integer('yxp')->nullable();
            $table->double('price')->default('0')->nullable();
            $table->string('about_me')->nullable();
            $table->text('image')->nullable();
            $table->string('status')->nullable();
            $table->integer('is_active')->default('0')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('gov_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};
