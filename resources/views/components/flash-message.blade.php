@props(['type' => 'success'])

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
     x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0"
     class="p-4 rounded text-sm flex items-center justify-between gap-4 {{ $type === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : 'bg-green-50 border border-green-200 text-green-700' }}">
    <div>{{ $slot }}</div>
    <button type="button" @click="show = false" class="text-lg leading-none shrink-0 opacity-60 hover:opacity-100">&times;</button>
</div>
