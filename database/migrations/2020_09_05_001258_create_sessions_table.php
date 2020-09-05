<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('track_id')->constrained();
            $table->unsignedTinyInteger('weather');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('track_temp');
            $table->unsignedTinyInteger('air_temp');
            $table->unsignedTinyInteger('formula');
            $table->boolean('main_race');
            $table->boolean('sprint_race');
            $table->unsignedTinyInteger('point_system')->default(0);
            $table->unsignedTinyInteger('laps');
            $table->unsignedSmallInteger('session_duration');
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
        Schema::dropIfExists('sessions');
    }
}
