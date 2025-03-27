<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $settings = Setting::where('user_id', $user->id)->first();
        
        return view('dashboard.salesperson.settings.index', compact('settings'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'pincode' => 'required|string|max:10',
            'address' => 'required|string',
            'designation' => 'required|string|max:100',
            'date_of_joining' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);
        
        $user->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        Auth::user()->update([
            'password' => bcrypt($request->password)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
    
    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();
        $settings = Setting::firstOrCreate(['user_id' => $user->id]);
        
        $settings->update([
            'whatsapp_notifications' => $request->whatsapp_notifications ?? false,
            'email_notifications' => $request->email_notifications ?? false,
            'sms_notifications' => $request->sms_notifications ?? false,
            'daily_summary' => $request->daily_summary ?? false,
            'weekly_report' => $request->weekly_report ?? false,
            'monthly_report' => $request->monthly_report ?? false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
            'settings' => $settings
        ]);
    }
    
    public function updateTargets(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'monthly_sales_target' => 'required|numeric|min:0',
            'monthly_leads_target' => 'required|integer|min:0',
            'quarterly_sales_target' => 'required|numeric|min:0',
            'quarterly_leads_target' => 'required|integer|min:0',
            'yearly_sales_target' => 'required|numeric|min:0',
            'yearly_leads_target' => 'required|integer|min:0'
        ]);
        
        $user->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Targets updated successfully',
            'user' => $user
        ]);
    }
} 