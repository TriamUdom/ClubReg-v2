<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('clubs', function (Blueprint $table) {
            $table->string('id', 6);
            $table->string('name', 75);
            $table->text('english_name');
            $table->boolean('is_audition');
            $table->boolean('is_active');
            $table->tinyInteger('subject_code'); // Deprecated
            $table->integer('fix_teacher'); // Deprecated
            $table->text('president_title');
            $table->text('president_fname');
            $table->text('president_lname');
            $table->text('adviser_title');
            $table->text('adviser_fname');
            $table->text('adviser_lname');
            $table->primary('id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('clubs');
    }
}
