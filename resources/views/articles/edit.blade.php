<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Article') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($article->status === 'rejected' && $article->rejection_reason)
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        <strong>{{ __('Rejected:') }}</strong> {{ $article->rejection_reason }}
                    </div>
                @endif

                <form method="POST" action="{{ route('articles.update', $article) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('articles.partials.form', ['article' => $article])

                    <div class="mt-6">
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                    </div>
                </form>

                @if ($article->images->isNotEmpty())
                    <div class="mt-4 pt-4 border-t">
                        <x-input-label :value="__('Current Body Images')" />
                        <div class="flex flex-wrap gap-3 mt-2">
                            @foreach ($article->images as $image)
                                <div class="relative">
                                    <img src="{{ $image->url() }}" class="h-20 w-20 object-cover rounded border">
                                    <button type="submit" form="delete-image-{{ $image->id }}"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 text-xs leading-5 text-center"
                                        title="{{ __('Remove image') }}">&times;</button>
                                    <form id="delete-image-{{ $image->id }}" method="POST"
                                        action="{{ route('articles.images.destroy', [$article, $image]) }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (in_array($article->status, ['draft', 'rejected']))
                    <form method="POST" action="{{ route('articles.submit', $article) }}" class="mt-4 pt-4 border-t">
                        @csrf
                        <p class="text-sm text-gray-500 mb-2">{{ __('Save your changes above first, then submit for admin approval.') }}</p>
                        <x-secondary-button type="submit">{{ __('Submit for Approval') }}</x-secondary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
