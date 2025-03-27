<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Plan;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class CreateMonthlyPlans extends Command
{
    protected $signature = 'plans:create-monthly';
    protected $description = 'Create monthly plans for salespersons';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle()
    {
        $nextMonth = Carbon::now()->addMonth();
        
        // Get all salespersons
        $salespersons = User::whereHas('role', function($query) {
            $query->where('name', 'salesperson');
        })->get();
        
        foreach ($salespersons as $user) {
            // Check if plan already exists
            $existingPlan = Plan::where('user_id', $user->id)
                ->where('month', $nextMonth->month)
                ->where('year', $nextMonth->year)
                ->first();
                
            if (!$existingPlan) {
                // Create new plan with default targets
                $plan = Plan::create([
                    'user_id' => $user->id,
                    'month' => $nextMonth->month,
                    'year' => $nextMonth->year,
                    'type' => 'monthly',
                    'lead_target' => $user->monthly_leads_target ?? 0,
                    'sales_target' => $user->monthly_sales_target ?? 0,
                    'description' => "Monthly plan for {$nextMonth->format('F Y')}",
                    'status' => 'draft',
                    'achievements' => [
                        'leads' => 0,
                        'sales' => 0,
                        'updated_at' => now()
                    ]
                ]);
                
                // Send WhatsApp notification
                $message = "Dear {$user->name},\n";
                $message .= "Your monthly plan for {$nextMonth->format('F Y')} has been created.\n";
                $message .= "Please review and update your targets.\n";
                $message .= "Lead Target: {$plan->lead_target}\n";
                $message .= "Sales Target: ₹{$plan->sales_target}";
                
                $this->whatsappService->sendMessage($user->whatsapp_number, $message);
                
                $this->info("Created plan for {$user->name}");
            }
        }
        
        $this->info("Completed creating monthly plans");
    }
} 