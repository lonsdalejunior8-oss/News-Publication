<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                {{ __('New Category') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <ul class="divide-y">
                    @forelse ($categories as $category)
                        <li class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">{{ $category->name }}</div>
                                <div class="text-sm text-gray-500">{{ $category->slug }} &middot; {{ $category->articles_count }} {{ __('articles') }}</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm text-gray-600 underline">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('Delete this category?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 underline">{{ __('Delete') }}</button>
                                </form>
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
