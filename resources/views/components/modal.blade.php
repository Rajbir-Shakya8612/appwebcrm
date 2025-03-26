@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'modal-sm',
    'md' => 'modal-md',
    'lg' => 'modal-lg',
    'xl' => 'modal-xl',
    '2xl' => 'modal-xl', // Bootstrap doesn’t have '2xl' by default, 'xl' is used
][$maxWidth];
@endphp

<!-- Modal -->
<div class="modal fade {{ $show ? 'show' : '' }}" tabindex="-1" role="dialog" aria-labelledby="{{ $name }}-label" aria-hidden="{{ !$show ? 'true' : 'false' }}" style="display: {{ $show ? 'block' : 'none' }};">
    <div class="modal-dialog {{ $maxWidth }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $name }}-label">{{ $name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.querySelector('.modal'));
        document.addEventListener('open-modal', function (event) {
            if (event.detail === '{{ $name }}') {
                modal.show();
            }
        });
        document.addEventListener('close-modal', function (event) {
            if (event.detail === '{{ $name }}') {
                modal.hide();
            }
        });
    });
</script>
