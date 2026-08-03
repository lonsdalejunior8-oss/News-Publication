<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $pending = Article::with(['author', 'category', 'images'])
            ->pending()
            ->latest()
            ->get();

        $request->user()->unreadNotifications->markAsRead();

        return view('admin.approvals', compact('pending'));
    }

    public function approve(Article $article)
    {
        abort_unless($article->status === 'pending', 403);

        $article->status = 'published';
        $article->published_at = now();
        $article->save();

        return back()->with('status', "Published \"{$article->title}\".");
    }

    public function reject(Request $request, Article $article)
    {
        abort_unless($article->status === 'pending', 403);

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $article->update([
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return back()->with('status', "Rejected \"{$article->title}\".");
    }
}
