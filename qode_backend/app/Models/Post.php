<?php

namespace App\Models;

use App\Services\ElasticsearchService;
use Illuminate\Database\Eloquent\Model;
class Post extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::created(function ($post) {
            $post->indexInElasticsearch();
        });
        static::updated(function ($post) {
            $post->indexInElasticsearch();
        });
        static::deleted(function ($post) {
            $post->removeFromElasticsearch();
        });
    }
    public function indexInElasticsearch()
    {
        app(ElasticsearchService::class)->index('posts', $this->id, [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
        ]);
    }

    public function removeFromElasticsearch() {
        app(ElasticsearchService::class)->delete('posts', $this->id);
    }

}
