<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("registrations", function (Blueprint $table) {
            $table->increments("id");
            $table->integer("student_id")->unsigned();
            $table->foreign("student_id")->references("id")->on("students");
            $table->integer("course_id")->unsigned();
            $table->foreign("course_id")->references("id")->on("courses");
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
        Schema::dropIfExists("registrations");
    }
}
