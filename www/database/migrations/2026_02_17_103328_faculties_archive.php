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
        Schema::create("faculties_archive", function (Blueprint $table) {
            $table->id();
            $table->text("faculty_id"); // Original faculty_id
            $table->text("first_name");
            $table->text("middle_name");
            $table->text("last_name");
            $table->text("department_id");
            $table->text("occupation");
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
