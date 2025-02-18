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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id');
            $table->enum('day_type', ['weekday', 'weekend']);
            $table->enum('ticket_type', ['adult', 'child']);
            $table->decimal('base_price', 8, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Make discount nullable
            $table->timestamps();

            // Add foreign key constraint for the movie_id
            $table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
