<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLgaCalculationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lga_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('lga_name')->unique();
            $table->float('sustainability_score');
            $table->float('future_score');
            $table->integer('additional_bus_stops');
            $table->integer('additional_train_stops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lga_calculations');
    }
}
