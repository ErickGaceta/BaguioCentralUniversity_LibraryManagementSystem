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
        Schema::create("courses", function (Blueprint $table) {
            $table->text("course_code")->primary();
            $table->text("department_id");
            $table->text("name");

            $table->index("name");

            $table->foreign("department_id")->references('department_code')->on('departments')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
