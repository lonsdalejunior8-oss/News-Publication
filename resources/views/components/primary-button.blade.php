<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-sig-blue border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sig-blue-dark focus:bg-sig-blue-dark active:bg-sig-blue-dark focus:outline-none focus:ring-2 focus:ring-sig-blue focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
