<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOldUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('old_users', function (Blueprint $table) {
            // We actually use just citizen_id and club_id
            // the others are added in case of future development, as imported data usually already have these values.
            $table->decimal('citizen_id', 13, 0); // Use this
            $table->decimal('student_id', 5, 0)->nullable();
            $table->tinyInteger('level')->nullable();
            $table->string('room', 5)->nullable();
            $table->integer('number')->nullable();
            $table->string('title')->nullable();
            $table->string('firstname', 75)->nullable();
            $table->string('lastname', 75)->nullable();
            $table->string('club_id', 6)->nullable(); // And this
            $table->primary('citizen_id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('old_users');
    }
}
