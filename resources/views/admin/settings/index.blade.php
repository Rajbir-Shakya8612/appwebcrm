@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Settings</h3>
                </div>
                <div class="card-body">
                    <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- General Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" name="company_name" value="{{ $settings->company_name ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Email</label>
                                            <input type="email" class="form-control" name="company_email" value="{{ $settings->company_email ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Phone</label>
                                            <input type="text" class="form-control" name="company_phone" value="{{ $settings->company_phone ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Address</label>
                                            <textarea class="form-control" name="company_address" rows="2">{{ $settings->company_address ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Attendance Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Working Hours Start</label>
                                            <input type="time" class="form-control" name="working_hours_start" value="{{ $settings->working_hours_start ?? '09:00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Working Hours End</label>
                                            <input type="time" class="form-control" name="working_hours_end" value="{{ $settings->working_hours_end ?? '18:00' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Late Threshold (minutes)</label>
                                            <input type="number" class="form-control" name="late_threshold" value="{{ $settings->late_threshold ?? 30 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Early Leave Threshold (minutes)</label>
                                            <input type="number" class="form-control" name="early_leave_threshold" value="{{ $settings->early_leave_threshold ?? 30 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Sales Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Default Sales Target</label>
                                            <input type="number" class="form-control" name="default_sales_target" value="{{ $settings->default_sales_target ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Commission Rate (%)</label>
                                            <input type="number" class="form-control" name="commission_rate" value="{{ $settings->commission_rate ?? 0 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Minimum Sale Amount</label>
                                            <input type="number" class="form-control" name="minimum_sale_amount" value="{{ $settings->minimum_sale_amount ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Maximum Sale Amount</label>
                                            <input type="number" class="form-control" name="maximum_sale_amount" value="{{ $settings->maximum_sale_amount ?? 0 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Notification Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="enable_email_notifications" value="1" {{ ($settings->enable_email_notifications ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">Enable Email Notifications</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="enable_sms_notifications" value="1" {{ ($settings->enable_sms_notifications ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">Enable SMS Notifications</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="notify_late_attendance" value="1" {{ ($settings->notify_late_attendance ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">Notify Late Attendance</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="notify_sales_target" value="1" {{ ($settings->notify_sales_target ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label">Notify Sales Target Achievement</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings updated successfully');
        } else {
            alert('Error updating settings');
        }
    });
});
</script>
@endpush 