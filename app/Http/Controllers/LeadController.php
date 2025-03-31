<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeadController extends Controller
{
    use AuthorizesRequests;

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
        $user = Auth::user();
        $leadStatuses = LeadStatus::with(['leads' => function($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orderBy('created_at', 'desc');
        }])->get();
            
        return view('dashboard.salesperson.leads.index', compact('leadStatuses'));
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'company' => 'required|string|max:255',
                'description' => 'required|string',
                'source' => 'required|string|max:255',
                'expected_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'status_id' => 'required|exists:lead_statuses,id'
            ]);

            $user = Auth::user();
            $today = now()->toDateString();

            // Check if the user has marked attendance today
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->exists();

            if (!$todayAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'आप आज की अटेंडेंस लगाए बिना नई लीड नहीं जोड़ सकते।'
                ], 403);
            }

            $lead = Lead::create([
                'user_id' => Auth::id(),
                'status_id' => $request->status_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'notes' => $request->description,
                'source' => $request->source,
                'expected_amount' => $request->expected_amount,
                'notes' => $request->notes
            ]);

            // Create initial activity
            $lead->createActivity(
                'Lead Created',
                'Lead was added to the system',
                Auth::user()
            );

            // Send WhatsApp notification to admin
            $admin = User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->first();

            if ($admin) {
                $this->whatsappService->sendNewLeadNotification($admin, $lead);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'lead' => $lead
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);
        return response()->json($lead);
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead)
    {
        try {
            $this->authorize('update', $lead);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'company' => 'required|string|max:255',
                'description' => 'required|string',
                'source' => 'required|string|max:255',
                'expected_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'status_id' => 'required|exists:lead_statuses,id'
            ]);

            $user = Auth::user();
            $today = now()->toDateString();

            // Check if the user has marked attendance today
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->exists();

            if (!$todayAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'आप आज की अटेंडेंस लगाए बिना नई लीड नहीं जोड़ सकते।'
                ], 403);
            }

            $lead->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'notes' => $request->description,
                'source' => $request->source,
                'expected_amount' => $request->expected_amount,
                'notes' => $request->notes,
                'status_id' => $request->status_id
            ]);

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
                'message' => 'Lead updated successfully',
                'lead' => $lead
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        
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
        $this->authorize('update', $lead);
        
        $request->validate([
            'status_id' => 'required|exists:lead_statuses,id'
        ]);

        $lead->update([
            'status_id' => $request->status_id
        ]);

        // If status changed to converted, send notification
        if ($request->status_id === 'converted') {
            $this->whatsappService->sendLeadConversionNotification($lead->user, $lead);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            'lead' => $lead
        ]);
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
    public function getLeadsByStatus(LeadStatus $status)
    {
        $leads = $status->leads()
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
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
        $user = Auth::user();
        $stats = [
            'total_leads' => Lead::where('user_id', $user->id)->count(),
            'leads_by_status' => Lead::where('user_id', $user->id)
                ->selectRaw('status_id, count(*) as count')
                ->groupBy('status_id')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->status_id => $item->count];
                }),
            'total_value' => Lead::where('user_id', $user->id)->sum('expected_value'),
            'conversion_rate' => $this->calculateConversionRate($user)
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function calculateConversionRate($user)
    {
        $totalLeads = Lead::where('user_id', $user->id)->count();
        if ($totalLeads === 0) return 0;
        
        $wonLeads = Lead::where('user_id', $user->id)
            ->whereHas('status', function($query) {
                $query->where('slug', 'won');
            })
            ->count();
            
        return round(($wonLeads / $totalLeads) * 100, 2);
    }
}
