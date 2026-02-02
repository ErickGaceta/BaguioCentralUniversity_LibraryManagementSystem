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
        Schema::create("archives_library", function (Blueprint $table) {
            $table->id();
            $table->integer("book_id");
            $table->text("title");
            $table->text("author");
            $table->date("publication_date");
            $table->text("publisher");
            $table->text("isbn");
            $table->text("department_id");
            $table->integer("copies");
            $table->timestamps();

            $table->index("title");

            $table->foreign("department_id")->references('department_code')->on('departments')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
