<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // database/migrations/..._add_coordinates_to_buildings_table.php

    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->float('map_x_percent')->nullable();
            $table->float('map_y_percent')->nullable();
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['map_x_percent', 'map_y_percent']);
        });
    }
};
