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
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#3B82F6');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Add status_id to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('status_id')->after('user_id')->constrained('lead_statuses')->onDelete('cascade');
        });

        // Insert default lead statuses
        DB::table('lead_statuses')->insert([
            [
                'name' => 'New Lead',
                'slug' => 'new',
                'color' => '#3B82F6',
                'description' => 'Newly created lead',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Contacted',
                'slug' => 'contacted',
                'color' => '#F59E0B',
                'description' => 'Initial contact made',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Qualified',
                'slug' => 'qualified',
                'color' => '#10B981',
                'description' => 'Lead has been qualified',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Proposal Sent',
                'slug' => 'proposal',
                'color' => '#8B5CF6',
                'description' => 'Proposal has been sent',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Negotiation',
                'slug' => 'negotiation',
                'color' => '#EC4899',
                'description' => 'In negotiation phase',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Closed Won',
                'slug' => 'won',
                'color' => '#059669',
                'description' => 'Lead converted to customer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Closed Lost',
                'slug' => 'lost',
                'color' => '#DC2626',
                'description' => 'Lead lost to competition',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
        
        Schema::dropIfExists('lead_statuses');
    }
}; 