<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys=ON;');

        Schema::create("departments", function (Blueprint $table) {
            $table->text('department_code')->primary();
            $table->text("name");

            $table->index("name");
        });
    }

};
