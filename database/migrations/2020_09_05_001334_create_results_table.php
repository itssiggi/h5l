<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('session_id')->constrained();
            $table->foreignId('team_id')->constrained();
            $table->unsignedTinyInteger('position');
            $table->unsignedTinyInteger('result_status');
            $table->boolean('fastest_lap');
            $table->unsignedTinyInteger('grid');
            $table->unsignedTinyInteger('penalties');
            $table->unsignedTinyInteger('pitstops');
            $table->unsignedDecimal('best_lap_time', 8, 3);
            $table->unsignedTinyInteger('laps');
            $table->float('race_time');
            $table->mediumText('youtube')->nullable();
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
        Schema::dropIfExists('results');
    }
}
