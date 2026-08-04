<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            // Absolute URL built from the actual request host (not the fixed APP_URL),
            // since this API is reachable via more than one hostname/IP.
            'featured_image_url' => $this->featured_image_path
                ? $request->getSchemeAndHttpHost().$this->featuredImageUrl()
                : null,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'author' => $this->whenLoaded('author', fn () => $this->author->name),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(
                fn ($image) => $request->getSchemeAndHttpHost().$image->url()
            )),
            'published_at' => $this->published_at?->toIso8601String(),
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
