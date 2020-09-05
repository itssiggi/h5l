<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafetycarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safetycars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained();
            $table->unsignedTinyInteger('begin');
            $table->unsignedTinyInteger('end');
            $table->boolean('virtualSC');
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
        Schema::dropIfExists('safetycars');
    }
}
