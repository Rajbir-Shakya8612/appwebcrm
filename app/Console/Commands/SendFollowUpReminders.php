<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;

class SendFollowUpReminders extends Command
{
 
    protected $signature = 'app:send-follow-up-reminders';

    protected $description = 'Send follow-up reminders for leads';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $leads = Lead::where('follow_up_date', $today)->get();

        foreach ($leads as $lead) {
            $this->whatsappService->sendLeadFollowUpReminder($lead->user, $lead);
        }

        $this->info('Follow-up reminders sent successfully.');
    }
}
