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
            $table->string('user_id')->nullable();
            $table->unsignedBigInteger('funder_id');
            $table->string('currency')->default('$');
            $table->string('budget');
            $table->date('deadline');
            $table->unsignedBigInteger('bids_count')->default(0);
            $table->string('status')->default('open');
            $table->text('areas_of_research');
            $table->string('area_1')->nullable()->default('');
            $table->string('area_2')->nullable()->default('');
            $table->string('area_3')->nullable()->default('');
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
