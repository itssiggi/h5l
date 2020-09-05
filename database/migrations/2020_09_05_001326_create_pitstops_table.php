<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePitstopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pitstops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('session_id')->constrained();
            $table->unsignedTinyInteger('lap');
            $table->unsignedTinyInteger('tyre_entry');
            $table->unsignedTinyInteger('tyre_exit');
            $table->unsignedDecimal('pitting_time', 6, 3);
            $table->unsignedDecimal('pitstop_time', 6, 3);
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
        Schema::dropIfExists('pitstops');
    }
}
