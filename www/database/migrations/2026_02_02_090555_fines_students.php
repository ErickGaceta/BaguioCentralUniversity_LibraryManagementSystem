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
        Schema::create("student_fines", function (Blueprint $table) {
            $table->id();
            $table->text("student_id");
            $table->text("copy_id");
            $table->text("amount");
            $table->text("reason");
            $table->timestamps();

            $table->foreign("student_id")->references('student_id')->on('students')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("copy_id")->references('copy_id')->on('copies')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
