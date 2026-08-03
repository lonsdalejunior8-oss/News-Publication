<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::whereHas('articles', fn ($q) => $q->published())
            ->orderBy('name')
            ->get();

        $articles = Article::with(['category', 'author'])
            ->published()
            ->when($request->query('category'), function ($query, $categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('public.news.index', compact('articles', 'categories'));
    }

    public function show(string $slug)
    {
        $article = Article::with(['category', 'author', 'images'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.news.show', compact('article'));
    }
}
