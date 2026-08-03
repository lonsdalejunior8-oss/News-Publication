<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Category;
use App\Models\User;
use App\Notifications\ArticleSubmittedForApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('articles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return redirect()->route('dashboard')->with('status', 'Article saved as draft.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $this->authorizeOwner($article);

        $categories = Category::orderBy('name')->get();

        return view('articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $this->authorizeOwner($article);

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

        return redirect()->route('dashboard')->with('status', 'Article updated.');
    }

    /**
     * Delete a single gallery image belonging to this article.
     */
    public function destroyImage(Article $article, ArticleImage $image)
    {
        $this->authorizeOwner($article);

        abort_unless($image->article_id === $article->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('status', 'Image removed.');
    }

    /**
     * Submit the article for admin approval.
     */
    public function submit(Article $article)
    {
        $this->authorizeOwner($article);

        abort_unless(in_array($article->status, ['draft', 'rejected']), 403);

        $article->update(['status' => 'pending', 'rejection_reason' => null]);

        Notification::send(
            User::where('role', 'admin')->get(),
            new ArticleSubmittedForApproval($article)
        );

        return redirect()->route('dashboard')->with('status', 'Article submitted for approval.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        $this->authorizeOwner($article);

        abort_unless(in_array($article->status, ['draft', 'rejected']), 403);

        if ($article->featured_image_path) {
            Storage::disk('public')->delete($article->featured_image_path);
        }

        foreach ($article->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $article->delete();

        return redirect()->route('dashboard')->with('status', 'Article deleted.');
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

    protected function authorizeOwner(Article $article): void
    {
        abort_unless(
            $article->author_id === request()->user()->id || request()->user()->isAdmin(),
            403
        );
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
