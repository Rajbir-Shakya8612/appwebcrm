<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\Plan;
use App\Models\Task;
use App\Models\Attendance;

class WhatsAppService
{
    protected $apiKey;
    protected $apiUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.api_key');
        $this->apiUrl = 'https://graph.facebook.com/v17.0/+918607807612/message';
    }
    
    public function sendMessage($number, $message)
    {
        try {
            if (empty($number) || strlen($number) < 10) {
                Log::error('Invalid phone number', ['number' => $number]);
                return false;
            }

            if (!str_starts_with($number, '+')) { // Agar number ke phle + nahi hai
                $number = '+91' . ltrim($number, '0'); // Zero hata ke +91 add karega
            }
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

    public function sendTaskAssignedNotification(User $user, Task $task)
    {
        $message = "Dear {$user->name}, a new task has been assigned to you:\n";
        $message .= "Title: {$task->title}\n";
        $message .= "Due Date: {$task->due_date->format('d M Y')}\n";
        $message .= "Priority: {$task->priority}\n";
        $message .= "Description: {$task->description}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendTaskStatusUpdateNotification(User $user, Task $task)
    {
        $message = "Dear {$user->name}, task status has been updated:\n";
        $message .= "Title: {$task->title}\n";
        $message .= "New Status: {$task->status}\n";
        $message .= "Due Date: {$task->due_date->format('d M Y')}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendTaskDueReminder(User $user, Task $task)
    {
        $message = "Dear {$user->name}, reminder: Task due soon:\n";
        $message .= "Title: {$task->title}\n";
        $message .= "Due Date: {$task->due_date->format('d M Y h:i A')}\n";
        $message .= "Priority: {$task->priority}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendTaskCompletedNotification(User $user, Task $task)
    {
        $message = "Dear {$user->name}, congratulations! Task completed:\n";
        $message .= "Title: {$task->title}\n";
        $message .= "Completed At: {$task->completed_at->format('d M Y h:i A')}\n";
        $message .= "Keep up the good work!";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendLeadStatusUpdateNotification(User $user, Lead $lead, $oldStatus, $newStatus)
    {
        $message = "Dear {$user->name}, lead status has been updated:\n";
        $message .= "Name: {$lead->name}\n";
        $message .= "Company: {$lead->company}\n";
        $message .= "Old Status: {$oldStatus}\n";
        $message .= "New Status: {$newStatus}\n";
        $message .= "Expected Value: ₹{$lead->expected_amount}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendLeadFollowUpReminder(User $user, Lead $lead)
    {
        $message = "Dear {$user->name}, follow-up reminder for lead:\n";
        $message .= "Name: {$lead->name}\n";
        $message .= "Company: {$lead->company}\n";
        $message .= "Follow-up Date: {$lead->follow_up_date->format('d M Y')}\n";
        $message .= "Current Status: {$lead->status}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendMeetingStatusUpdateNotification(User $user, Meeting $meeting, $oldStatus, $newStatus)
    {
        $message = "Dear {$user->name}, meeting status has been updated:\n";
        $message .= "Title: {$meeting->title}\n";
        $message .= "Date: {$meeting->meeting_date->format('d M Y h:i A')}\n";
        $message .= "Old Status: {$oldStatus}\n";
        $message .= "New Status: {$newStatus}\n";
        $message .= "Location: {$meeting->location}";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendMeetingCancellationNotification(User $user, Meeting $meeting)
    {
        $message = "Dear {$user->name}, meeting has been cancelled:\n";
        $message .= "Title: {$meeting->title}\n";
        $message .= "Date: {$meeting->meeting_date->format('d M Y h:i A')}\n";
        $message .= "Location: {$meeting->location}\n";
        $message .= "Reason: " . (isset($meeting->cancellation_reason) ? $meeting->cancellation_reason : 'Not specified');
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendAttendanceStatusUpdateNotification(User $user, Attendance $attendance, $oldStatus, $newStatus)
    {
        $message = "Dear {$user->name}, your attendance status has been updated:\n";
        $message .= "Date: {$attendance->date->format('d M Y')}\n";
        $message .= "Old Status: {$oldStatus}\n";
        $message .= "New Status: {$newStatus}\n";
        if ($attendance->check_in_time) {
            $message .= "Check-in Time: {$attendance->check_in_time->format('h:i A')}\n";
        }
        if ($attendance->check_out_time) {
            $message .= "Check-out Time: {$attendance->check_out_time->format('h:i A')}\n";
        }
        if ($attendance->working_hours) {
            $message .= "Working Hours: {$attendance->working_hours}";
        }
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendMonthlyPerformanceSummary(User $user, $month, $year, $stats)
    {
        $message = "Dear {$user->name}, here's your monthly performance summary for {$month}/{$year}:\n";
        $message .= "Total Leads: {$stats['total_leads']}\n";
        $message .= "Converted Leads: {$stats['converted_leads']}\n";
        $message .= "Total Sales: ₹{$stats['total_sales']}\n";
        $message .= "Attendance: {$stats['attendance_percentage']}%\n";
        $message .= "Tasks Completed: {$stats['tasks_completed']}\n";
        $message .= "Meetings Attended: {$stats['meetings_attended']}\n";
        $message .= "Target Achievement: {$stats['target_achievement']}%";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }

    public function sendWeeklyProgressUpdate(User $user, $weekStart, $weekEnd, $stats)
    {
        $message = "Dear {$user->name}, here's your weekly progress update:\n";
        $message .= "Period: {$weekStart->format('d M')} - {$weekEnd->format('d M Y')}\n";
        $message .= "New Leads: {$stats['new_leads']}\n";
        $message .= "Sales: ₹{$stats['sales']}\n";
        $message .= "Tasks Completed: {$stats['tasks_completed']}\n";
        $message .= "Meetings: {$stats['meetings']}\n";
        $message .= "Attendance: {$stats['attendance_percentage']}%";
        
        return $this->sendMessage($user->whatsapp_number, $message);
    }
} 