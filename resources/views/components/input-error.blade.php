@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'text-danger']) }}>
        <ul class="list-unstyled mb-0">
            @foreach ((array) $messages as $message)
                <li class="small">{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
