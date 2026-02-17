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
        Schema::create("student_borrows", function (Blueprint $table) {
            $table->id();
            $table->text("student_id");
            $table->text("copy_id");
            $table->text("ref_number");
            $table->text("return_ref_number")->nullable();
            $table->datetime("date_borrowed");
            $table->date("due_date");
            $table->datetime("date_returned")->nullable();

            $table->foreign("student_id")->references('student_id')->on( 'students')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("copy_id")->references('copy_id')->on('copies')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
