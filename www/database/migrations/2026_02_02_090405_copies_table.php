<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("copies", function (Blueprint $table) {
            $table->text("copy_id")->primary();
            $table->integer("book_id");
            $table->text("course_id");
            $table->text("status")->default('available');
            $table->timestamps();

            $table->foreign("course_id")->references('course_code')->on('courses')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("book_id")->references('id')->on('books')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

};
