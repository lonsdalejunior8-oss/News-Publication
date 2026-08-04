<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Fixed categorical order (never reassigned by value) so an author
     * keeps the same color across page loads regardless of ranking.
     */
    protected const PALETTE = ['#2a78d6', '#eb6834', '#1baf7a', '#eda100', '#e87ba4', '#008300', '#4a3aa7', '#e34948'];

    public function index()
    {
        $published = Article::published()->with(['author', 'category'])->get();

        $categoryLabels = $published
            ->map(fn ($a) => $a->category?->name ?? 'Uncategorized')
            ->unique()
            ->sort()
            ->values();

        $authorNames = $published->pluck('author.name')->unique()->sort()->values();

        $authorSeries = $authorNames->values()->map(function ($authorName, $i) use ($published, $categoryLabels) {
            return [
                'label' => $authorName,
                'color' => self::PALETTE[$i % count(self::PALETTE)],
                'data' => $categoryLabels->map(function ($categoryName) use ($published, $authorName) {
                    return $published
                        ->filter(fn ($a) => $a->author->name === $authorName
                            && ($a->category?->name ?? 'Uncategorized') === $categoryName)
                        ->count();
                })->values(),
            ];
        });

        $statusCounts = Article::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byStatus = collect(['draft', 'pending', 'rejected', 'published'])
            ->mapWithKeys(fn ($status) => [$status => $statusCounts->get($status, 0)]);

        return view('admin.dashboard', [
            'categoryLabels' => $categoryLabels,
            'authorSeries' => $authorSeries,
            'statusLabels' => $byStatus->keys()->map(fn ($s) => ucfirst($s)),
            'statusCounts' => $byStatus->values(),
            'stats' => [
                'published' => $byStatus['published'],
                'pending' => $byStatus['pending'],
                'authors' => User::where('role', 'author')->count(),
                'categories' => Category::whereHas('articles', fn ($q) => $q->published())->count(),
            ],
        ]);
    }
}
