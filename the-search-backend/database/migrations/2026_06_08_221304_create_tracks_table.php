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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('spotify_id')->nullable(false)->unique();
            $table->foreignId('album_id')->constrained('albums')->cascadeOnDelete();
            $table->string('name')->nullable(false);
            $table->string('number')->nullable(false);
            $table->enum('status', 
                ['grey', 'crossed', 'brown', 'orange', 'blue', 'green', 'pink', 'red'])
                ->default('grey');
            $table->integer('seconds');
            //TODO: need to figure out if we want an artist id here or not
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
