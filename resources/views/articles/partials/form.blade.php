<div x-data="{
        featuredPreview: {{ $article?->featuredImageUrl() ? \Illuminate\Support\Js::from($article->featuredImageUrl()) : 'null' }},
        galleryPreviews: [],
        onFeaturedChange(e) {
            const file = e.target.files[0];
            if (file) this.featuredPreview = URL.createObjectURL(file);
        },
        onGalleryChange(e) {
            this.galleryPreviews = Array.from(e.target.files).map(f => URL.createObjectURL(f));
        },
    }">
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
            value="{{ old('title', $article?->title) }}" required autofocus />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="category_id" :value="__('Category')" />
        <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sig-blue focus:ring-sig-blue">
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
        <textarea id="excerpt" name="excerpt" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sig-blue focus:ring-sig-blue">{{ old('excerpt', $article?->excerpt) }}</textarea>
        <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="body" :value="__('Body')" />
        <textarea id="body" name="body" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-sig-blue focus:ring-sig-blue" required>{{ old('body', $article?->body) }}</textarea>
        <x-input-error :messages="$errors->get('body')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="featured_image" :value="__('Featured Image')" />
        <img x-show="featuredPreview" :src="featuredPreview" x-cloak class="mt-2 mb-2 h-32 w-32 object-cover rounded">
        <input id="featured_image" name="featured_image" type="file" accept="image/*" @change="onFeaturedChange"
            class="mt-1 block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-sig-blue file:text-white file:text-sm file:font-medium hover:file:bg-sig-blue-dark file:cursor-pointer cursor-pointer">
        <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="images" :value="__('Body Images')" />
        <p class="text-sm text-gray-500 mb-2">{{ __('Add extra images to display alongside this article (up to 10).') }}</p>

        <div x-show="galleryPreviews.length" x-cloak class="flex flex-wrap gap-2 mb-2">
            <template x-for="src in galleryPreviews" :key="src">
                <img :src="src" class="h-16 w-16 object-cover rounded border">
            </template>
        </div>

        <input id="images" name="images[]" type="file" accept="image/*" multiple @change="onGalleryChange"
            class="mt-1 block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-sig-blue file:text-white file:text-sm file:font-medium hover:file:bg-sig-blue-dark file:cursor-pointer cursor-pointer">
        <x-input-error :messages="$errors->get('images')" class="mt-2" />
        <x-input-error :messages="$errors->get('images.*')" class="mt-1" />
    </div>
</div>
