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
            $table->string('id', 8);
            $table->string('name', 75);
            $table->text('english_name');
            $table->boolean('is_audition');
            $table->boolean('is_active');
            $table->text('description')->nullable();
            $table->tinyInteger('subject_code')->nullable(); // Deprecated
            $table->unsignedSmallInteger('max_member');
            $table->json('user_id');
            $table->text('audition_location')->nullable();
            $table->text('audition_time')->nullable();
            $table->text('audition_instruction')->nullable();
            $table->text('location')->nullable();
            $table->text('president_title')->nullable();
            $table->text('president_fname')->nullable();
            $table->text('president_lname')->nullable();
            $table->text('president_phone')->nullable();
            $table->text('adviser_title')->nullable();
            $table->text('adviser_fname')->nullable();
            $table->text('adviser_lname')->nullable();
            $table->text('adviser_phone')->nullable();
            $table->timestamps();
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
