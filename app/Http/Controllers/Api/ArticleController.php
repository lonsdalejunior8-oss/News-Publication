<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Published articles, optionally filtered by category slug.
     */
    public function index(Request $request)
    {
        $articles = Article::with(['category', 'author', 'images'])
            ->published()
            ->when($request->query('category'), function ($query, $categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->latest('published_at')
            ->paginate(15);

        return ArticleResource::collection($articles);
    }

    /**
     * Display a single published article by slug.
     */
    public function show(string $slug)
    {
        $article = Article::with(['category', 'author', 'images'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new ArticleResource($article);
    }
}
