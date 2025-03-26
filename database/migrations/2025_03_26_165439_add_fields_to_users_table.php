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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->default(3)->constrained('roles')->onDelete('cascade');
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('pincode')->nullable();
            $table->text('address')->nullable();
            $table->string('location')->nullable();
            $table->string('designation')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('status')->default('active');
            $table->json('settings')->nullable();
            $table->decimal('target_amount', 10, 2)->nullable();
            $table->integer('target_leads')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id',
                'phone',
                'photo',
                'whatsapp_number',
                'pincode',
                'address',
                'location',
                'designation',
                'date_of_joining',
                'status',
                'settings',
                'target_amount',
                'target_leads'
            ]);
        });
    }
};
