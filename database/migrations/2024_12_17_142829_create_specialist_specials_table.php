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
        Schema::create('specialist_specials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('specialist_id')->nullable();
            $table->bigInteger('special_id')->nullable();
            $table->string('job_name_ar')->nullable();
            $table->string('job_name_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialist_specials');
    }
};
