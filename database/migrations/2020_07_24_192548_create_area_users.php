<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_users', function (Blueprint $table) {
            $table->foreignId('area_id')->constrained()
                    ->cascade('onDelete');
            $table->foreignId('user_id')->constrained()
                    ->cascade('onDelete');
            $table->primary(['area_id','user_id']);
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
        Schema::dropIfExists('area_users');
    }
}
