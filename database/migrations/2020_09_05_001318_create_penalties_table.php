<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('session_id')->constrained();
            $table->unsignedTinyInteger('lap');
            $table->unsignedTinyInteger('penalty_type');
            $table->unsignedTinyInteger('infringement_type');
            $table->foreignId('other_driver_id')->nullable()->constrained('drivers');
            $table->unsignedTinyInteger('time')->nullable();
            $table->unsignedTinyInteger('placesGained')->nullable();
            $table->boolean('reverted')->default(0);
            $table->boolean('stewards')->default(0);
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
        Schema::dropIfExists('penalties');
    }
}
