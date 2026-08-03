@extends('layouts.public')

@section('title', config('app.name').' — News')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('News') }}</h1>

        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('news.index') }}"
                   class="px-3 py-1 rounded-full text-sm {{ request('category') ? 'bg-white border border-gray-200 text-gray-600' : 'bg-gray-800 text-white' }}">
                    {{ __('All') }}
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('news.index', ['category' => $category->slug]) }}"
                       class="px-3 py-1 rounded-full text-sm {{ request('category') === $category->slug ? 'bg-gray-800 text-white' : 'bg-white border border-gray-200 text-gray-600' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="space-y-6">
        @forelse ($articles as $article)
            <article class="bg-white shadow-sm rounded-lg overflow-hidden sm:flex">
                @if ($article->featured_image_path)
                    <img src="{{ $article->featuredImageUrl() }}"
                         class="w-full sm:w-48 h-40 object-cover">
                @endif
                <div class="p-6 flex-1">
                    @if ($article->category)
                        <div class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ $article->category->name }}</div>
                    @endif
                    <h2 class="text-lg font-semibold text-gray-900 mb-1">
                        <a href="{{ route('news.show', $article->slug) }}" class="hover:underline">{{ $article->title }}</a>
                    </h2>
                    <p class="text-gray-600 text-sm mb-2">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->body), 160) }}</p>
                    <div class="text-xs text-gray-400">
                        {{ $article->published_at?->format('M j, Y') }} &middot; {{ $article->author->name }}
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white shadow-sm rounded-lg p-6 text-gray-500">
                {{ __('No articles published yet.') }}
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $articles->links() }}
    </div>
@endsection
