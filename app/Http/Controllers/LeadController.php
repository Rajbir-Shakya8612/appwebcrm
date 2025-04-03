<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeadStatus;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Notification as NotificationFacade;

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
        try {
            $user = Auth::user();
            $leadStatuses = LeadStatus::all();
            $leads = Lead::with('status')
                ->where('user_id', $user->id)
                ->when(request('status'), function($query, $status) {
                    if ($status !== 'all') {
                        $query->whereHas('status', function($q) use ($status) {
                            $q->where('id', $status);
                        });
                    }
                })
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('dashboard.salesperson.leads.index', compact('leadStatuses', 'leads'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading leads: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate(Lead::getValidationRules());

            // Check attendance
            $attendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', now()->toDateString())
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please mark your attendance before creating a lead.'
                ], 422);
            }

            // Create lead
            $lead = Lead::create([
                'user_id' => Auth::id(),
                'status_id' => $validated['status_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company' => $validated['company'],
                'additional_info' => json_encode($validated['description']),
                'description' => $validated['description'] ?? null,
                'source' => $validated['source'] ?? null,
                'expected_amount' => $validated['expected_amount'],
                'notes' => $validated['notes'] ?? null,
                'follow_up_date' => $validated['follow_up_date'],
                'location' => $validated['location'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude']
            ]);

            // Send notification to admin
            $admin = User::whereHas('role', function($query) {
                $query->where('name', 'admin');
            })->first();

            if ($admin) {
                $this->whatsappService->sendNewLeadNotification($admin, $lead);
            }
            // Send notification to the user who created the lead
            $this->whatsappService->sendNewLeadNotification($lead->user, $lead);

            // Create initial activity
            $lead->createActivity(
                'Lead Created',
                'Lead was added to the system',
                Auth::user()
            );

            // Create follow-up notification
            Notification::create([
                'user_id' => Auth::id(),
                'type' => 'lead_follow_up',
                'title' => 'Lead Follow-up',
                'message' => "Follow up with {$lead->name} on {$lead->follow_up_date->format('M d, Y')}",
                'data' => ['lead_id' => $lead->id],
                'notifiable_id' => $lead->id,
                'notifiable_type' => Lead::class
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully',
                'lead' => $lead
            ]);
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
        try {
            $this->authorize('view', $lead);
            $lead->load('status');
            
            if (request()->wantsJson()) {
                return response()->json($lead);
            }
            
            return view('dashboard.salesperson.leads.show', compact('lead'));
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading lead details',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error loading lead details: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead)
    {
        try {
            // Validate request
            $validated = $request->validate(Lead::getValidationRules(true, $lead->id));

            // Check attendance
            $attendance = Attendance::where('user_id', Auth::id())
                ->whereDate('date', now()->toDateString())
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please mark your attendance before updating a lead.'
                ], 422);
            }

            // Handle additional_info - ensure it's proper JSON
            $additionalInfo = $request->input('description');
            if (is_string($additionalInfo)) {
                $additionalInfo = json_decode($additionalInfo, true) ?? [];
            }

            // Get the old status before update
            $oldStatusId = $lead->status_id;

            // Update lead
            $lead->update([
                'status_id' => $validated['status_id'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company' => $validated['company'],
                'additional_info' => $additionalInfo,
                'source' => $validated['source'] ?? null,
                'expected_amount' => $validated['expected_amount'],
                'notes' => $validated['notes'] ?? null,
                'description' => $validated['description'] ?? null,
                'follow_up_date' => $validated['follow_up_date'],
                'location' => $validated['location'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude']
            ]);

            // Create activity for the update
            $lead->createActivity(
                'Lead Updated',
                'Lead information was updated',
                Auth::user()
            );

            // Reload the lead with status relationship
            $lead->load('status');

            // Check if status changed to converted
            $convertedStatusId = LeadStatus::where('slug', 'converted')->value('id');
            
            if ($convertedStatusId && $validated['status_id'] == $convertedStatusId && $oldStatusId != $convertedStatusId) {
                // Send notification to admin
                $admin = User::whereHas('role', function($query) {
                    $query->where('slug', 'admin');
                })->first();

                if ($admin) {
                    $message = "Lead converted by " . Auth::user()->name . "\n";
                    $message .= "Name: " . $lead->name . "\n";
                    $message .= "Phone: " . $lead->phone . "\n";
                    $message .= "Amount: " . $lead->expected_amount;
                    
                    $this->whatsappService->sendMessage($admin->phone, $message);
                  
                    $this->whatsappService->sendLeadConversionNotification($admin, $lead);
                

                }
                // Send notification to salesperson
                $this->whatsappService->sendLeadConversionNotification($lead->user, $lead);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully',
                'lead' => $lead
            ]);
            
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
        try {
            $this->authorize('delete', $lead);

            // Optionally send a notification before deleting
            $this->whatsappService->sendMessage($lead->user->whatsapp_number, "Lead '{$lead->name}' has been deleted.");
            $lead->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead deleted successfully'
                ]);
            }

            return redirect()->route('salesperson.leads.index')
                ->with('success', 'Lead deleted successfully');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete lead',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to delete lead: ' . $e->getMessage());
        }
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
