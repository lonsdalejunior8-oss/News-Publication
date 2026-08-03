<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ArticleSubmittedForApproval extends Notification
{
    use Queueable;

    public function __construct(protected Article $article)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'title' => $this->article->title,
            'author' => $this->article->author->name,
        ];
    }
}
