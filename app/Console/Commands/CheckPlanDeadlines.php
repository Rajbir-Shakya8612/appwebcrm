<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class CheckPlanDeadlines extends Command
{
    protected $signature = 'plans:check-deadlines';
    protected $description = 'Check for plan deadlines and send notifications';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle()
    {
        $now = Carbon::now();
        
        // Get all active plans
        $plans = Plan::with('user')
            ->where('status', 'active')
            ->get();
            
        foreach ($plans as $plan) {
            // Get end date based on plan type
            switch ($plan->type) {
                case 'monthly':
                    $endDate = Carbon::create($plan->year, $plan->month, 1)->endOfMonth();
                    break;
                case 'quarterly':
                    $endDate = Carbon::create($plan->year, $plan->month, 1)->endOfQuarter();
                    break;
                case 'yearly':
                    $endDate = Carbon::create($plan->year, 1, 1)->endOfYear();
                    break;
                default:
                    continue;
            }
            
            // Check if plan is ending in 3 days
            if ($now->diffInDays($endDate) === 3) {
                $message = "Dear {$plan->user->name},\n";
                $message .= "Your {$plan->type} plan is ending in 3 days.\n";
                $message .= "Current achievements:\n";
                $message .= "Leads: {$plan->achievements['leads']}/{$plan->lead_target}\n";
                $message .= "Sales: ₹{$plan->achievements['sales']}/₹{$plan->sales_target}";
                
                $this->whatsappService->sendMessage($plan->user->whatsapp_number, $message);
                
                $this->info("Sent deadline reminder for {$plan->user->name}'s {$plan->type} plan");
            }
            
            // Check if plan has ended
            if ($now->isAfter($endDate)) {
                $plan->update(['status' => 'completed']);
                
                $message = "Dear {$plan->user->name},\n";
                $message .= "Your {$plan->type} plan has ended.\n";
                $message .= "Final achievements:\n";
                $message .= "Leads: {$plan->achievements['leads']}/{$plan->lead_target}\n";
                $message .= "Sales: ₹{$plan->achievements['sales']}/₹{$plan->sales_target}";
                
                $this->whatsappService->sendMessage($plan->user->whatsapp_number, $message);
                
                $this->info("Marked plan as completed for {$plan->user->name}");
            }
        }
        
        $this->info("Completed checking plan deadlines");
    }
} 