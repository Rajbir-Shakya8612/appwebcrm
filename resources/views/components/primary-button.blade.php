<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-dark text-uppercase font-weight-bold px-4 py-2']) }}>
    {{ $slot }}
</button>
