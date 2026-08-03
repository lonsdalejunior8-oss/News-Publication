@extends('layouts.public')

@section('title', $article->title.' — '.config('app.name'))

@section('content')
    <div x-data="{ lightboxOpen: false, lightboxSrc: null }">
        <a href="{{ route('news.index') }}" class="text-sm text-gray-500 hover:underline">&larr; {{ __('Back to News') }}</a>

        <article class="bg-white shadow-sm rounded-lg overflow-hidden mt-4">
            @if ($article->featured_image_path)
                <img src="{{ $article->featuredImageUrl() }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-8">
                @if ($article->category)
                    <div class="text-xs font-semibold text-gray-500 uppercase mb-2">{{ $article->category->name }}</div>
                @endif

                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $article->title }}</h1>

                <div class="text-sm text-gray-400 mb-6">
                    {{ $article->published_at?->format('F j, Y') }} &middot; {{ $article->author->name }}
                </div>

                <div class="whitespace-pre-line leading-relaxed text-gray-800">{{ $article->body }}</div>

                @if ($article->images->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-6">
                        @foreach ($article->images as $image)
                            <img src="{{ $image->url() }}"
                                 class="w-full h-32 object-cover rounded cursor-pointer transition-transform duration-150 hover:scale-105"
                                 @click="lightboxOpen = true; lightboxSrc = '{{ $image->url() }}'">
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <div x-show="lightboxOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
             @click="lightboxOpen = false"
             @keydown.escape.window="lightboxOpen = false">
            <img :src="lightboxSrc" class="max-h-[90vh] max-w-[90vw] rounded shadow-lg" @click.stop>
            <button @click="lightboxOpen = false"
                    class="absolute top-4 right-4 text-white text-3xl leading-none">&times;</button>
        </div>
    </div>
@endsection
