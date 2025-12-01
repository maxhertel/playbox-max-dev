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
        Schema::create('jukebox_queues', function ($table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('track_name');
            $table->string('track_uri');
            $table->integer('is_playing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jukebox_queues');
    }
};
