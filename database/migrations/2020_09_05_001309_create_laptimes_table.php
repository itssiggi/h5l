<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaptimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laptimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('session_id')->constrained();
            $table->unsignedTinyInteger('lap');
            $table->unsignedDecimal('sector_one', 7, 3);
            $table->unsignedDecimal('sector_two', 7, 3);
            $table->unsignedDecimal('sector_three', 7, 3);
            $table->unsignedDecimal('time', 8, 3);
            $table->boolean('boxlap');
            $table->boolean('valid');
            $table->boolean('tyre');
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
        Schema::dropIfExists('laptimes');
    }
}
