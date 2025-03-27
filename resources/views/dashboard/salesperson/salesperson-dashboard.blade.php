@extends('layouts.salesperson')

@section('title', 'Salesperson Dashboard')

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
                    <p class="text-lg font-semibold">{{ $totalLeads }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Monthly Sales</p>
                    <p class="text-lg font-semibold">₹{{ number_format($monthlySales, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Today's Meetings</p>
                    <p class="text-lg font-semibold">{{ $todayMeetings }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                    <i class="fas fa-bullseye text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Target Achievement</p>
                    <p class="text-lg font-semibold">{{ $targetAchievement }}%</p>
                </div>
            </div>
        </div>
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
                        <h4 class="text-sm font-semibold text-gray-700 mb-4">{{ $status->name }}</h4>
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
                                        <a href="tel:{{ $lead->phone }}" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        <a href="mailto:{{ $lead->email }}" class="text-green-500 hover:text-green-700">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        <a href="https://wa.me/{{ $lead->phone }}" target="_blank" class="text-green-500 hover:text-green-700">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editLead({{ $lead->id }})" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteLead({{ $lead->id }})" class="text-red-500 hover:text-red-700">
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

    <!-- Calendar -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold">Upcoming Meetings</h3>
        </div>
        <div class="p-6">
            <div id="calendar"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Dragula
    const containers = document.querySelectorAll('[id^="status-"]');
    dragula(containers, {
        moves: function(el) {
            return el.classList.contains('cursor-move');
        },
        accepts: function(el, target) {
            return target.id !== el.parentNode.id;
        },
        direction: 'horizontal',
        revertOnSpill: true
    }).on('drop', function(el, target) {
        const leadId = el.dataset.leadId;
        const newStatusId = target.id.replace('status-', '');
        
        // Update lead status via AJAX
        $.ajax({
            url: `/leads/${leadId}/status`,
            method: 'PUT',
            data: {
                status_id: newStatusId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Lead status updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update lead status'
                });
            }
        });
    });

    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: @json($meetings),
        eventClick: function(info) {
            Swal.fire({
                title: info.event.title,
                html: `
                    <p class="text-sm text-gray-600">${info.event.extendedProps.description}</p>
                    <p class="text-sm text-gray-600">Location: ${info.event.extendedProps.location}</p>
                    <p class="text-sm text-gray-600">Time: ${info.event.start.toLocaleTimeString()}</p>
                `,
                icon: 'info'
            });
        }
    });
    calendar.render();
});

// Lead Management Functions
function editLead(leadId) {
    // Implement lead editing functionality
    Swal.fire({
        title: 'Edit Lead',
        html: `
            <form id="editLeadForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            // Implement save functionality
            return true;
        }
    });
}

function deleteLead(leadId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement delete functionality
            $.ajax({
                url: `/leads/${leadId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Lead has been deleted.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Remove the lead card from the DOM
                    $(`[data-lead-id="${leadId}"]`).remove();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete lead'
                    });
                }
            });
        }
    });
}
</script>
@endpush 