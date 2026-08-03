<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Categories that have at least one published article.
     */
    public function index()
    {
        return Category::whereHas('articles', fn ($q) => $q->published())
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }
}
