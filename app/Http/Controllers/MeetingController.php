<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MeetingController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $meetings = Meeting::where('user_id', $user->id)
            ->orderBy('meeting_date')
            ->get();
            
        return view('dashboard.salesperson.meetings.index', compact('meetings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'meeting_date' => 'required|date|after:now',
            'reminder_date' => 'required|date|before:meeting_date',
            'location' => 'required|string',
            'attendees' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $meeting = Meeting::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'meeting_date' => $request->meeting_date,
            'reminder_date' => $request->reminder_date,
            'location' => $request->location,
            'attendees' => $request->attendees,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting scheduled successfully',
            'meeting' => $meeting
        ]);
    }

    public function show(Meeting $meeting)
    {
        $this->authorize('view', $meeting);
        return view('dashboard.salesperson.meetings.show', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'meeting_date' => 'required|date',
            'reminder_date' => 'required|date|before:meeting_date',
            'location' => 'required|string',
            'attendees' => 'required|array',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $meeting->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Meeting updated successfully',
            'meeting' => $meeting
        ]);
    }

    public function destroy(Meeting $meeting)
    {
        $this->authorize('delete', $meeting);
        
        if ($meeting->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete completed meetings'
            ], 403);
        }
        
        $meeting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meeting deleted successfully'
        ]);
    }

    public function getPendingReminders()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        $reminders = Meeting::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('reminder_date', '<=', $now)
            ->where('meeting_date', '>', $now)
            ->get();
            
        return response()->json([
            'success' => true,
            'reminders' => $reminders
        ]);
    }

    public function markAsCompleted(Meeting $meeting)
    {
        $this->authorize('update', $meeting);
        
        if ($meeting->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Meeting is not in pending status'
            ], 400);
        }
        
        $meeting->update(['status' => 'completed']);
        
        return response()->json([
            'success' => true,
            'message' => 'Meeting marked as completed',
            'meeting' => $meeting
        ]);
    }
} 