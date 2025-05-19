<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionRestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_rests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_work_id')->constrained()->cascadeOnDelete();
            $table->dateTime('rest_start');
            $table->dateTime('rest_finish')->nullable();
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
        Schema::dropIfExists('correction_rests');
    }
}
