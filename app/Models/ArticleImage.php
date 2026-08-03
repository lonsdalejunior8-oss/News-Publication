<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'position'])]
class ArticleImage extends Model
{
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Root-relative so it resolves under whichever host/IP served the page.
     */
    public function url(): string
    {
        return '/storage/'.$this->path;
    }
}
