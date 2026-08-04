<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Approvals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-flash-message>{{ session('status') }}</x-flash-message>
            @endif

            @if (session('error'))
                <x-flash-message type="error">{{ session('error') }}</x-flash-message>
            @endif

            @forelse ($pending as $article)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 transition hover:shadow-md">
                    <div class="flex justify-between items-start gap-6">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $article->title }}</h3>
                            <div class="text-sm text-gray-500 mb-2">
                                {{ __('By') }} {{ $article->author->name }}
                                &middot; {{ $article->category?->name ?? __('Uncategorized') }}
                                &middot; {{ $article->created_at->format('M j, Y') }}
                            </div>

                            @if ($article->featured_image_path)
                                <img src="{{ $article->featuredImageUrl() }}" class="h-24 w-24 object-cover rounded mb-2">
                            @endif

                            <p class="text-gray-700 whitespace-pre-line">{{ $article->excerpt ?: Str::limit($article->body, 300) }}</p>

                            @if ($article->images->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach ($article->images as $image)
                                        <img src="{{ $image->url() }}" class="h-14 w-14 object-cover rounded border">
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2 w-48">
                            <form method="POST" action="{{ route('admin.approvals.approve', $article) }}">
                                @csrf
                                <x-primary-button class="w-full justify-center">{{ __('Approve & Publish') }}</x-primary-button>
                            </form>

                            <form method="POST" action="{{ route('admin.approvals.reject', $article) }}" class="space-y-2">
                                @csrf
                                <textarea name="rejection_reason" rows="2" placeholder="{{ __('Reason for rejection') }}" class="w-full text-sm border-gray-300 rounded-md focus:border-sig-blue focus:ring-sig-blue" required></textarea>
                                <x-danger-button class="w-full justify-center">{{ __('Reject') }}</x-danger-button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-500">
                    {{ __('No articles are waiting for approval.') }}
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
