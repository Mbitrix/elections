<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precincts', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('34A')->nullable();
            $table->string('link')->nullable();
            $table->integer('county_id')->nullable();
            $table->integer('constituency_id')->nullable();
            $table->integer('ward_id')->nullable();
            $table->integer('rao')->nullable();
            $table->integer('wsr')->nullable();
            $table->integer('wajk')->nullable();
            $table->integer('mwaure')->nullable();
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
        Schema::dropIfExists('precincts');
    }
};