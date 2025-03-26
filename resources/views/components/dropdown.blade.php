@props(['align' => 'end', 'width' => 'auto', 'contentClasses' => 'py-1 bg-white'])

<div class="dropdown">
    <!-- Dropdown Toggle Button -->
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $trigger }}
    </button>

    <!-- Dropdown Menu -->
    <ul class="dropdown-menu {{ $align === 'start' ? 'dropdown-menu-start' : 'dropdown-menu-end' }}" aria-labelledby="dropdownMenuButton" style="width: {{ $width }};">
        <li>
            <div class="rounded-3 border border-gray-300 shadow-sm {{ $contentClasses }}">
                {{ $content }}
            </div>
        </li>
    </ul>
</div>

{{-- <!-- Include Bootstrap JS if not already included -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.1/js/bootstrap.bundle.min.js"></script> --}}
