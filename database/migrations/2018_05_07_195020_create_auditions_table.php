<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('auditions', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('citizen_id', 13, 0)->unsigned()->index();
            $table->string('club_id', 6);
            $table->enum('status', ['AWAITING', 'FAILED', 'PASSED', 'REJECTED', 'JOINED'])->default('AWAITING');
            $table->text('comment')->nullable();
            
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('auditions');
    }
}
