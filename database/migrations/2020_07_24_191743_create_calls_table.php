<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('funder_id');
            $table->string('currency')->default('$');
            $table->string('budget');
            $table->date('deadline');
            $table->string('status')->default('open');
            $table->string('areas_of_research');
            $table->string('area_1')->nullable()->default('');
            $table->string('area_2')->nullable()->default('');
            $table->string('area_3')->nullable()->default('');
            $table->json('areas_of_research_names');
            $table->string('description');
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
        Schema::dropIfExists('calls');
    }
}
