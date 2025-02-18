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
        Schema::table('theatres', function (Blueprint $table) {
            $table->integer('rows_count')->after('name');        // Add after 'name' column
            $table->integer('seats_per_row')->after('rows_count'); // Add after 'rows_count' column
        });
    }

    public function down(): void
    {
        Schema::table('theatres', function (Blueprint $table) {
            $table->dropColumn(['rows_count', 'seats_per_row']);
        });
    }
};
