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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('spotify_id')->nullable(false)->unique();
            $table->string('name')->nullable(false);
            $table->date('release_date')->nullable(false);
            $table->double('pure_score')->nullable();
            $table->double('vibe_score')->nullable();
            $table->enum('status', ['to-listen', 'listening', 'listened'])->default('to-listen');
            $table->string('image_url')->default("someURL");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
