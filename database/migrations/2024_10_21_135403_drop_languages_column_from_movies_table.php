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
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('languages'); // Drop the languages column
        });
    }

    public function down()
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->json('languages')->nullable(); // You can re-add it if you ever roll back this migration
        });
    }
};
