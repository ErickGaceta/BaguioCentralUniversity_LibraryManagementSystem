<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives_copies', function (Blueprint $table) {
            $table->id();
            $table->integer('archived_book_id');   // FK → archives_library.id
            $table->integer('original_book_id');   // original books.id (for reference)
            $table->text('copy_id');               // original copy_id string
            $table->text('course_id');
            $table->text('status')->default('Available');
            $table->text('condition')->default('Good');
            $table->timestamps();

            $table->foreign('archived_book_id')
                ->references('id')
                ->on('archives_library')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives_copies');
    }
};
