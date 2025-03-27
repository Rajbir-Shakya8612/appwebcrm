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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User who owns the lead
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'lost', 'shared'])->default('new');
            $table->text('notes')->nullable();
            $table->decimal('expected_amount', 10, 2)->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('source')->nullable();
            $table->string('location')->nullable();
            $table->json('additional_info')->nullable(); // JSON column for extra details
            
            // Foreign Key Constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
