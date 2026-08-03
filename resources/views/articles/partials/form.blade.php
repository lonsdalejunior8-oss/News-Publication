<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
        value="{{ old('title', $article?->title) }}" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="category_id" :value="__('Category')" />
    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        <option value="">{{ __('— None —') }}</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $article?->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="excerpt" :value="__('Excerpt')" />
    <textarea id="excerpt" name="excerpt" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('excerpt', $article?->excerpt) }}</textarea>
    <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="body" :value="__('Body')" />
    <textarea id="body" name="body" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('body', $article?->body) }}</textarea>
    <x-input-error :messages="$errors->get('body')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="featured_image" :value="__('Featured Image')" />
    @if ($article?->featured_image_path)
        <img src="{{ $article->featuredImageUrl() }}" class="mt-2 mb-2 h-32 w-32 object-cover rounded">
    @endif
    <input id="featured_image" name="featured_image" type="file" accept="image/*" class="mt-1 block w-full">
    <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="images" :value="__('Body Images')" />
    <p class="text-sm text-gray-500 mb-2">{{ __('Add extra images to display alongside this article (up to 10).') }}</p>

    <input id="images" name="images[]" type="file" accept="image/*" multiple class="mt-1 block w-full">
    <x-input-error :messages="$errors->get('images')" class="mt-2" />
    <x-input-error :messages="$errors->get('images.*')" class="mt-1" />
</div>
