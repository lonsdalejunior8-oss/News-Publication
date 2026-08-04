<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticleSubmittedForApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class MyArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with(['category', 'images'])
            ->where('author_id', $request->user()->id)
            ->latest()
            ->get();

        return ArticleResource::collection($articles);
    }

    public function store(Request $request)
    {
        $data = $this->validateArticle($request);

        $article = new Article($data);
        $article->author_id = $request->user()->id;
        $article->slug = Article::generateUniqueSlug($data['title']);
        $article->status = 'draft';

        if ($request->hasFile('featured_image')) {
            $article->featured_image_path = $request->file('featured_image')->store('articles', 'public');
        }

        $article->save();

        $this->storeGalleryImages($request, $article);

        return new ArticleResource($article->load(['category', 'images']));
    }

    public function update(Request $request, Article $article)
    {
        abort_unless($article->author_id === $request->user()->id, 403);

        $data = $this->validateArticle($request);
        $article->fill($data);

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image_path) {
                Storage::disk('public')->delete($article->featured_image_path);
            }
            $article->featured_image_path = $request->file('featured_image')->store('articles', 'public');
        }

        $article->save();

        $this->storeGalleryImages($request, $article);

        return new ArticleResource($article->load(['category', 'images']));
    }

    public function submit(Request $request, Article $article)
    {
        abort_unless($article->author_id === $request->user()->id, 403);
        abort_unless(in_array($article->status, ['draft', 'rejected'], true), 403);

        $article->update(['status' => 'pending', 'rejection_reason' => null]);

        Notification::send(
            User::where('role', 'admin')->get(),
            new ArticleSubmittedForApproval($article)
        );

        return new ArticleResource($article->load(['category', 'images']));
    }

    protected function storeGalleryImages(Request $request, Article $article): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $nextPosition = $article->images()->max('position') + 1;

        foreach ($request->file('images') as $file) {
            $article->images()->create([
                'path' => $file->store('articles', 'public'),
                'position' => $nextPosition++,
            ]);
        }
    }

    protected function validateArticle(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:5120'],
        ]);
    }
}
