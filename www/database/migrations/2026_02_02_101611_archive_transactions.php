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
        Schema::create("transaction_archives", function (Blueprint $table) {
            $table->id();
            $table->text("student_borrow_transaction_id")->nullable();
            $table->text("faculty_borrow_transaction_id")->nullable();
            $table->text("library_transaction_id")->nullable();
            $table->text("name");
            $table->timestamps();
        });
    }
};
