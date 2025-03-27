<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\Plan;

class WhatsAppService
{
    protected $apiKey;
    protected $apiUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key');
        $this->apiUrl = config('services.whatsapp.api_url');
    }
    
    public function sendMessage($number, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $number,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);
            
            if (!$response->successful()) {
                Log::error('WhatsApp API error', [
                    'number' => $number,
                    'message' => $message,
                    'response' => $response->json()
                ]);
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'number' => $number,
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    public function sendLateNotification(User $user)
    {
        $message = "Dear {$user->name}, you are late for work today. Please ensure punctuality.";
        return $this->sendMessage($user->whatsapp_number, $message);
    }
    
    public function sendAttendanceConfirmation(User $user, $status)
    {
        $message = "Dear {$user->name}, your attendance has been marked as {$status} for today.";
        return $this->sendMessage($user->whatsapp_number, $message);
    }
    
    public function sendNewLeadNotification(User $user, Lead $lead)
    {
        $message = "Dear {$user->name}, a new lead has been assigned to you:\n";
        $message .= "Name: {$lead->name}\n";
        $message .= "Phone: {$lead->phone}\n";
        $message .= "Status: {$lead->status}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }
    
    public function sendLeadConversionNotification(User $user, Lead $lead)
    {
        $message = "Dear {$user->name}, congratulations! A lead has been converted:\n";
        $message .= "Name: {$lead->name}\n";
        $message .= "Company: {$lead->company}\n";
        $message .= "Expected Value: {$lead->expected_value}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }
    
    public function sendTargetUpdate(User $user, $target, $achieved)
    {
        $message = "Dear {$user->name}, here's your target update:\n";
        $message .= "Target: {$target}\n";
        $message .= "Achieved: {$achieved}\n";
        $message .= "Remaining: " . ($target - $achieved);
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }
    
    public function sendDailySummary(User $user, $leads, $sales)
    {
        $message = "Dear {$user->name}, here's your daily summary:\n";
        $message .= "New Leads: {$leads}\n";
        $message .= "Sales: {$sales}\n";
        $message .= "Keep up the good work!";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendMeetingReminder(User $user, Meeting $meeting)
    {
        $message = "Dear {$user->name}, reminder for your upcoming meeting:\n";
        $message .= "Title: {$meeting->title}\n";
        $message .= "Date: {$meeting->meeting_date->format('d M Y h:i A')}\n";
        $message .= "Location: {$meeting->location}\n";
        $message .= "Description: {$meeting->description}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendPlanCreatedNotification(User $user, Plan $plan)
    {
        $message = "Dear {$user->name},\n";
        $message .= "Your {$plan->type} plan for {$plan->month}/{$plan->year} has been created.\n";
        $message .= "Please review and update your targets:\n";
        $message .= "Lead Target: {$plan->lead_target}\n";
        $message .= "Sales Target: ₹{$plan->sales_target}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendPlanAchievementUpdate(User $user, Plan $plan)
    {
        $leadPercentage = $plan->getLeadAchievementPercentage();
        $salesPercentage = $plan->getSalesAchievementPercentage();
        
        $message = "Dear {$user->name},\n";
        $message .= "Here's your {$plan->type} plan progress update:\n";
        $message .= "Leads: {$plan->achievements['leads']}/{$plan->lead_target} ({$leadPercentage}%)\n";
        $message .= "Sales: ₹{$plan->achievements['sales']}/₹{$plan->sales_target} ({$salesPercentage}%)";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }
} 