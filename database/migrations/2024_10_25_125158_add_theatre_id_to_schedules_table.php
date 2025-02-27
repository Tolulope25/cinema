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
        Schema::table('schedules', function (Blueprint $table) {
            // Adding the theatre_id column and setting up a foreign key constraint
            $table->foreignId('theatre_id')->constrained()->onDelete('cascade')->after('movie_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Dropping the theatre_id column if rolling back the migration
            $table->dropForeign(['theatre_id']);
            $table->dropColumn('theatre_id');
        });
    }
};
