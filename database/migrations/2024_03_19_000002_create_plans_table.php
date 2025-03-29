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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('month');
            $table->integer('year');
            $table->enum('type', ['monthly', 'quarterly', 'yearly']);
            $table->integer('lead_target');
            $table->decimal('sales_target', 12, 2);
            $table->text('description');
            $table->enum('status', ['draft', 'active', 'completed'])->default('active');
            $table->json('achievements');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add unique constraint for user_id, month, and year combination
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
}; 