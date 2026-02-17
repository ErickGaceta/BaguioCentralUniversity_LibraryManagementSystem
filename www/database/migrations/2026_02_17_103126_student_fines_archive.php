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
        Schema::create("student_fines_archive", function (Blueprint $table) {
            $table->id();
            $table->integer("fine_id"); // Original fine ID
            $table->text("student_id");
            $table->text("copy_id");
            $table->text("amount");
            $table->text("reason");
            $table->integer("status");
            $table->date("date_paid")->nullable();
            $table->timestamp("archived_at");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
