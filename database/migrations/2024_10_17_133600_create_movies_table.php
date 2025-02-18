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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration');
            $table->date('release_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('languages', 100)->nullable();
            $table->string('director', 100)->nullable();
            $table->text('cast')->nullable();
            $table->string('poster_url', 255)->nullable(); // URL for the poster image
            $table->string('trailer_url', 255)->nullable(); // URL for the trailer

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
