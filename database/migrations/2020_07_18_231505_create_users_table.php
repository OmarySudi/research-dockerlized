<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('mobile_number');
            $table->string('faculty');
            $table->string('department');
            $table->string('areas_of_research')->nullable()->default('');
            $table->string('area_1')->nullable()->default('');
            $table->string('area_2')->nullable()->default('');
            $table->string('area_3')->nullable()->default('');
            $table->json('areas_of_research_names')->nullable();
            $table->boolean('research_system_admin_admin')->default(0);
            $table->string('research_system_admin_role')->nullable()->default('');
            $table->string('password');
            $table->string('password_confirm');
            $table->string('middle_name')->nullable()->default('');
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
        Schema::dropIfExists('users');
    }
}
