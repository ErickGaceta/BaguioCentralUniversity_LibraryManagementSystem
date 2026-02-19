<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('copy_accessions', function (Blueprint $table) {
            $table->id();
            $table->text('copy_id');
            $table->text('accession_number');
            $table->text('call_number');
            $table->timestamps();

            $table->unique('copy_id');
            $table->unique('accession_number');

            $table->foreign('copy_id')
                ->references('copy_id')
                ->on('copies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('copy_accessions');
    }
};
