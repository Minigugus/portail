<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', validation_max('name'))->unique();
            $table->boolean('is_spring')->default(0);
            $table->char('year', 2);

            $table->timestamp('begining_at')->unique()->nullable();
            // Le début du semestre
            $table->timestamp('ending_at')->unique()->nullable();
            // La fin du semestre
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
        Schema::dropIfExists('semesters');
    }
}
