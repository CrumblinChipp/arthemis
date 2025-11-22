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
        Schema::create('report_entries', function (Blueprint $table) {
            $table->id(); // report_id (primary key)

            // date of collection
            $table->date('date');

            // Building where waste was collected
            $table->unsignedBigInteger('building_id');

            // User who submitted the entry
            $table->unsignedBigInteger('user_id');

            // nature of the report
            $table->text('description');
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_entries');
    }
};
