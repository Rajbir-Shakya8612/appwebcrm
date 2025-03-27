<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meeting;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class CheckMeetingReminders extends Command
{
    protected $signature = 'meetings:check-reminders';
    protected $description = 'Check for pending meeting reminders and send notifications';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle()
    {
        $now = Carbon::now();
        
        $meetings = Meeting::with('user')
            ->where('status', 'pending')
            ->where('reminder_date', '<=', $now)
            ->where('meeting_date', '>', $now)
            ->get();
            
        foreach ($meetings as $meeting) {
            // Send WhatsApp notification
            $message = "Reminder: You have a meeting scheduled.\n";
            $message .= "Title: {$meeting->title}\n";
            $message .= "Date: {$meeting->meeting_date->format('d M Y h:i A')}\n";
            $message .= "Location: {$meeting->location}";
            
            $this->whatsappService->sendMessage($meeting->user->whatsapp_number, $message);
            
            $this->info("Sent reminder for meeting: {$meeting->title}");
        }
        
        $this->info("Checked " . $meetings->count() . " meeting reminders");
    }
} 