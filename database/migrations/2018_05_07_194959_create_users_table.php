<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->decimal('citizen_id', 13, 0);
            $table->decimal('student_id', 5, 0)->nullable();
            $table->tinyInteger('level')->nullable();
            $table->string('room', 5);
            $table->integer('number')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname', 75);
            $table->string('lastname', 75)->nullable();
            $table->string('club_id', 6)->nullable();
            $table->string('reason')->nullable();
            $table->string('comment', 100)->nullable();
            $table->timestamps();
            $table->primary('citizen_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
}
