@props(['label', 'value', 'dot' => null])

<div class="bg-white shadow-sm rounded-lg p-5 flex items-center gap-3">
    @if ($dot)
        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $dot }}"></span>
    @endif
    <div>
        <div class="text-sm text-gray-500">{{ $label }}</div>
        <div class="text-2xl font-semibold text-gray-900">{{ $value }}</div>
    </div>
</div>
