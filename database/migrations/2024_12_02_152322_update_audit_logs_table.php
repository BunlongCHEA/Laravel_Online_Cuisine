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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('email')->nullable(); // Email of the user
            $table->string('status')->nullable(); // Status (e.g., success/unauthorized)
            $table->string('model')->nullable();
            $table->string('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('status');
            $table->dropColumn('model');
            $table->dropColumn('data');
        });
    }
};
