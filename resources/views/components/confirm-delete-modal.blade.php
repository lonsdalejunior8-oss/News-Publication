@props(['id', 'action', 'title', 'body'])

<span x-data="" x-on:click.prevent="$dispatch('open-modal', '{{ $id }}')" class="cursor-pointer">
    {{ $slot }}
</span>

<x-modal :name="$id" max-width="md">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">{{ $title }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ $body }}</p>

        <form method="POST" action="{{ $action }}" class="mt-6 flex justify-end gap-3">
            @csrf
            @method('DELETE')
            <x-secondary-button type="button" x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-danger-button>
                {{ __('Delete') }}
            </x-danger-button>
        </form>
    </div>
</x-modal>
