<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_entries', function (Blueprint $table) {
            $table->id(); // waste_id (primary key)

            // date of collection
            $table->date('date');

            // Building where waste was collected
            $table->unsignedBigInteger('building_id');

            // User who submitted the entry
            $table->unsignedBigInteger('user_id');

            // Waste amounts (kg)
            $table->integer('residual')->default(0);
            $table->integer('recyclable')->default(0);
            $table->integer('biodegradable')->default(0);
            $table->integer('infectious')->default(0);
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_entries');
    }
};
