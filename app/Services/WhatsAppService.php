<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Lead;

class WhatsAppService
{
    private $apiKey;
    private $phoneNumber;
    private $apiUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key');
        $this->phoneNumber = config('services.whatsapp.phone_number');
        $this->apiUrl = config('services.whatsapp.api_url');
    }
    
    public function sendMessage($to, $message)
    {
        try {
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'phone' => $to,
                'message' => $message
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('WhatsApp notification failed: ' . $e->getMessage());
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
} 