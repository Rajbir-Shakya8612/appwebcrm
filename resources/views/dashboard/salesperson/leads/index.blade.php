@extends('layouts.salesperson')

@section('title', 'Leads Management')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Leads</p>
                    <p class="text-lg font-semibold">{{ $leadStatuses->sum(function($status) { return $status->leads->count(); }) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Won Leads</p>
                    <p class="text-lg font-semibold">{{ $leadStatuses->firstWhere('slug', 'won')->leads->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-lg font-semibold">{{ $leadStatuses->whereIn('slug', ['contacted', 'qualified', 'proposal', 'negotiation'])->sum(function($status) { return $status->leads->count(); }) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Value</p>
                    <p class="text-lg font-semibold">₹{{ number_format($leadStatuses->sum(function($status) { return $status->leads->sum('expected_value'); }), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Lead Button -->
    <div class="flex justify-end">
        <button onclick="addLead()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i> Add New Lead
        </button>
    </div>

    <!-- Kanban Board -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Lead Pipeline</h3>
        </div>
        <div class="p-6">
            <div class="flex space-x-6 overflow-x-auto pb-4">
                @foreach($leadStatuses as $status)
                <div class="flex-shrink-0 w-80">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-semibold text-gray-700">{{ $status->name }}</h4>
                            <span class="text-xs text-gray-500">{{ $status->leads->count() }}</span>
                        </div>
                        <div class="space-y-4" id="status-{{ $status->id }}">
                            @foreach($status->leads as $lead)
                            <div class="bg-white rounded-lg shadow p-4 cursor-move" data-lead-id="{{ $lead->id }}">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="font-medium text-gray-900">{{ $lead->name }}</h5>
                                    <span class="text-xs text-gray-500">{{ $lead->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($lead->description, 100) }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <a href="tel:{{ $lead->phone }}" class="text-blue-500 hover:text-blue-700 lead-action" data-action="call" data-phone="{{ $lead->phone }}">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        <a href="mailto:{{ $lead->email }}" class="text-green-500 hover:text-green-700 lead-action" data-action="email" data-email="{{ $lead->email }}">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        <a href="https://wa.me/{{ $lead->phone }}" target="_blank" class="text-green-500 hover:text-green-700 lead-action" data-action="whatsapp" data-phone="{{ $lead->phone }}">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editLead({{ $lead->id }})" class="text-blue-500 hover:text-blue-700 lead-action" data-action="edit" data-lead-id="{{ $lead->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteLead({{ $lead->id }})" class="text-red-500 hover:text-red-700 lead-action" data-action="delete" data-lead-id="{{ $lead->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addLead() {
    Swal.fire({
        title: 'Add New Lead',
        html: `
            <form id="addLeadForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company</label>
                    <input type="text" name="company" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Source</label>
                    <select name="source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="website">Website</option>
                        <option value="referral">Referral</option>
                        <option value="social">Social Media</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expected Value</label>
                    <input type="number" name="expected_value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Lead',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const form = document.getElementById('addLeadForm');
            const formData = new FormData(form);
            
            return $.ajax({
                url: '{{ route("salesperson.leads.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Lead added successfully',
                timer: 1500,
                showConfirmButton: false
            });
            // Refresh the page to show the new lead
            window.location.reload();
        }
    });
}
</script>
@endpush 