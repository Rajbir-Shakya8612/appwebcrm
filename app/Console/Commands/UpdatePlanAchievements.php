<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Models\Lead;
use App\Models\Sale;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class UpdatePlanAchievements extends Command
{
    protected $signature = 'plans:update-achievements';
    protected $description = 'Update achievements for all active plans';

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
            // Get start and end dates based on plan type
            switch ($plan->type) {
                case 'monthly':
                    $startDate = Carbon::create($plan->year, $plan->month, 1)->startOfMonth();
                    $endDate = $startDate->copy()->endOfMonth();
                    break;
                case 'quarterly':
                    $startDate = Carbon::create($plan->year, $plan->month, 1)->startOfQuarter();
                    $endDate = $startDate->copy()->endOfQuarter();
                    break;
                case 'yearly':
                    $startDate = Carbon::create($plan->year, 1, 1)->startOfYear();
                    $endDate = $startDate->copy()->endOfYear();
                    break;
                default:
                    continue 2;
            }
            
            // Get achievements
            $leads = Lead::where('user_id', $plan->user_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $sales = Sale::where('user_id', $plan->user_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');
                
            // Update plan achievements
            $plan->updateAchievements($leads, $sales);
            
            // Send WhatsApp notification if significant change
            $oldLeads = $plan->achievements['leads'] ?? 0;
            $oldSales = $plan->achievements['sales'] ?? 0;
            
            if ($leads != $oldLeads || $sales != $oldSales) {
                $this->whatsappService->sendPlanAchievementUpdate($plan->user, $plan);
            }
            
            $this->info("Updated achievements for {$plan->user->name}'s {$plan->type} plan");
        }
        
        $this->info("Completed updating plan achievements");
    }
} 