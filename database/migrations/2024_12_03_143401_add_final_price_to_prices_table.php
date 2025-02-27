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
        Schema::table('prices', function (Blueprint $table) {
            $table->decimal('final_price', 8, 2)->nullable(); // Add final_price column
        });
    }

    public function down()
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('final_price'); // Remove final_price column
        });
    }

};
