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
            $table->decimal('student_id', 5, 0);
            $table->decimal('citizen_id', 13, 0)->nullable();
            $table->tinyInteger('level')->nullable();
            $table->string('room', 5);
            $table->integer('number');
            $table->string('title');
            $table->string('firstname', 75);
            $table->string('lastname', 75);
            $table->string('club_id', 6);
            $table->string('reason');
            $table->string('comment', 100);
            
            $table->primary('student_id');
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
