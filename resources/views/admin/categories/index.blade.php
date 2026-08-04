<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-sig-blue text-white rounded-md text-sm font-medium hover:bg-sig-blue-dark">
                {{ __('New Category') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-flash-message>{{ session('status') }}</x-flash-message>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <ul class="divide-y">
                    @forelse ($categories as $category)
                        <li class="px-6 py-4 flex items-center justify-between transition hover:bg-gray-50">
                            <div>
                                <div class="font-medium text-gray-900">{{ $category->name }}</div>
                                <div class="text-sm text-gray-500">{{ $category->slug }} &middot; {{ $category->articles_count }} {{ __('articles') }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm text-gray-600 underline">{{ __('Edit') }}</a>
                                <x-confirm-delete-modal
                                    :id="'delete-category-'.$category->id"
                                    :action="route('admin.categories.destroy', $category)"
                                    :title="__('Delete this category?')"
                                    :body="__('This cannot be undone. Categories with existing articles can\'t be deleted.')">
                                    <span class="text-sm text-red-600 underline">{{ __('Delete') }}</span>
                                </x-confirm-delete-modal>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-4 text-gray-500">{{ __('No categories yet.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
