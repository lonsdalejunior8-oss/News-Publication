<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;

class DashboardController extends Controller
{
    public function index()
    {
        $byAuthor = Article::published()
            ->selectRaw('author_id, count(*) as total')
            ->groupBy('author_id')
            ->with('author')
            ->get()
            ->sortByDesc('total')
            ->values();

        $statusCounts = Article::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byStatus = collect(['draft', 'pending', 'rejected', 'published'])
            ->mapWithKeys(fn ($status) => [$status => $statusCounts->get($status, 0)]);

        return view('admin.dashboard', [
            'authorLabels' => $byAuthor->pluck('author.name'),
            'authorCounts' => $byAuthor->pluck('total'),
            'statusLabels' => $byStatus->keys()->map(fn ($s) => ucfirst($s)),
            'statusCounts' => $byStatus->values(),
        ]);
    }
}
