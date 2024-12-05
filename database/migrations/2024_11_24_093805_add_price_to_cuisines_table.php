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
        Schema::table('cuisines', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->after('description')->nullable()->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuisines', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
