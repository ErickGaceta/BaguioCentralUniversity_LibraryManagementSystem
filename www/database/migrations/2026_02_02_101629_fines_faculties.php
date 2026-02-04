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
        Schema::create("faculty_fines", function (Blueprint $table) {
            $table->id();
            $table->text("faculty_id");
            $table->text("copy_id");
            $table->text("amount");
            $table->text("reason");
            $table->integer("status")->default(0);
            $table->date("date_paid")->nullable();
            $table->timestamps();

            $table->foreign("faculty_id")->references('faculty_id')->on('faculties')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign("copy_id")->references('copy_id')->on('copies')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
