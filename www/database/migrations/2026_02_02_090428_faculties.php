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
        Schema::create("faculties", function (Blueprint $table) {
            $table->text("faculty_id")->primary();
            $table->text("first_name");
            $table->text("middle_name");
            $table->text("last_name");
            $table->text("department_id");
            $table->text("occupation");
            $table->timestamps();

            $table->index(["first_name", "middle_name", "last_name"]);

            $table->foreign("department_id")->references('department_code')->on('departments')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
