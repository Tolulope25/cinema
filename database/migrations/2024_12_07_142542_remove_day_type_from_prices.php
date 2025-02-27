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
        // Remove the 'day_type' column
        $table->dropColumn('day_type');
    });
}

public function down()
{
    Schema::table('prices', function (Blueprint $table) {
        // Optionally, you can add the column back if you roll back the migration
        $table->string('day_type')->nullable(); // Adjust the type if needed
    });


        Schema::table('prices', function (Blueprint $table) {
            //
        });
    }
};
