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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->text('title');

            $table->text('report_type');

            $table->text('period_preset');

            $table->date('date_from');
            $table->date('date_to');

            $table->json('report_data')->nullable();

            $table->integer('total_records')->default(0);

            $table->timestamps();

            $table->index('report_type');
            $table->index('period_preset');
            $table->index(['date_from', 'date_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
