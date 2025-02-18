<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('movie_theatre', function (Blueprint $table) {
            $table->date('show_date')->nullable(); // Add show_date column
            $table->time('show_time')->nullable(); // Add show_time column
        });
    }

    public function down()
    {
        Schema::table('movie_theatre', function (Blueprint $table) {
            $table->dropColumn(['show_date', 'show_time']);
        });
    }
};
