<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class LeadController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display a listing of the leads.
     */
    public function index()
    {
        $leads = Lead::where('user_id', Auth::id())
            ->with(['activities' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'leads' => $leads
        ]);
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'source' => 'required|string|in:website,referral,social,other',
            'status' => 'required|string|in:new,contacted,qualified,proposal,negotiation,converted,lost',
            'notes' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'expected_value' => 'nullable|numeric|min:0'
        ]);

        $lead = Lead::create([
            'user_id' => Auth::id(),
            ...$validated
        ]);

        // Create initial activity
        $lead->createActivity(
            'Lead Created',
            'Lead was added to the system',
            Auth::user()
        );

        // Send WhatsApp notification to admin
        $admin = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->first();

        if ($admin) {
            $this->whatsappService->sendNewLeadNotification($admin, $lead);
        }

        return response()->json([
            'success' => true,
            'lead' => $lead
        ]);
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        Gate::authorize('view', $lead);

        $lead->load('activities');

        return view('dashboard.salesperson.leads.show', compact('lead'));
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead)
    {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'source' => 'required|string|in:website,referral,social,other',
            'status' => 'required|string|in:new,contacted,qualified,proposal,negotiation,converted,lost',
            'notes' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
            'expected_value' => 'nullable|numeric|min:0'
        ]);

        $lead->update($validated);

        // Create activity for the update
        $lead->createActivity(
            'Lead Updated',
            'Lead information was updated',
            Auth::user()
        );

        // If status changed to converted, send notification
        if ($lead->wasChanged('status') && $lead->status === 'converted') {
            $this->whatsappService->sendLeadConversionNotification($lead->user, $lead);
        }

        return response()->json([
            'success' => true,
            'lead' => $lead
        ]);
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead)
    {
        Gate::authorize('delete', $lead);

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully'
        ]);
    }

    /**
     * Update the lead status.
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'status' => 'required|string|in:new,contacted,qualified,proposal,negotiation,converted,lost'
        ]);

        if ($lead->updateStatus($validated['status'], Auth::user())) {
            // If status changed to converted, send notification
            if ($validated['status'] === 'converted') {
                $this->whatsappService->sendLeadConversionNotification($lead->user, $lead);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update lead status'
        ], 500);
    }

    /**
     * Schedule a follow up for the lead.
     */
    public function scheduleFollowUp(Request $request, Lead $lead)
    {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'next_follow_up' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        if ($lead->scheduleFollowUp(
            $validated['next_follow_up'],
            $validated['notes'] ?? null,
            Auth::user()
        )) {
            return response()->json([
                'success' => true,
                'message' => 'Follow up scheduled successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to schedule follow up'
        ], 500);
    }

    /**
     * Get leads by status.
     */
    public function getLeadsByStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,contacted,qualified,proposal,negotiation,converted,lost'
        ]);

        $leads = Lead::where('user_id', Auth::id())
            ->where('status', $validated['status'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'leads' => $leads
        ]);
    }

    /**
     * Get lead statistics.
     */
    public function getLeadStats()
    {
        $stats = [
            'total' => Lead::where('user_id', Auth::id())->count(),
            'converted' => Lead::where('user_id', Auth::id())->where('status', 'converted')->count(),
            'lost' => Lead::where('user_id', Auth::id())->where('status', 'lost')->count(),
            'pipeline' => Lead::where('user_id', Auth::id())
                ->whereNotIn('status', ['converted', 'lost'])
                ->count(),
            'by_status' => Lead::where('user_id', Auth::id())
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
