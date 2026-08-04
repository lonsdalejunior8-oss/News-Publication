<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the current author's own articles, grouped by category.
     */
    public function index(Request $request)
    {
        $articles = $request->user()->articles()->with('category')->latest()->get();

        $articlesByCategory = $articles->groupBy(fn ($article) => $article->category?->name ?? 'Uncategorized');

        $statusCounts = $articles->countBy('status');
        $byStatus = collect(['draft', 'pending', 'rejected', 'published'])
            ->mapWithKeys(fn ($status) => [$status => $statusCounts->get($status, 0)]);

        $byCategory = $articles->where('status', 'published')
            ->groupBy(fn ($article) => $article->category?->name ?? 'Uncategorized')
            ->map->count()
            ->sortDesc();

        return view('dashboard', [
            'articlesByCategory' => $articlesByCategory,
            'statusLabels' => $byStatus->keys()->map(fn ($s) => ucfirst($s)),
            'statusCounts' => $byStatus->values(),
            'categoryLabels' => $byCategory->keys(),
            'categoryCounts' => $byCategory->values(),
            'stats' => [
                'total' => $articles->count(),
                'draft' => $byStatus['draft'],
                'pending' => $byStatus['pending'],
                'published' => $byStatus['published'],
            ],
        ]);
    }
}
