<?php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    protected $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }
    public function search(Request $request)
    {
        $query = 'Tempore iste dolor';

        // Define the fields you want to search across
        $fields = ['title', 'excerpt' , 'description', 'meta_title', 'meta_description', 'keywords'];

        $results = $this->elasticsearch->searchAllColumns('posts', $query, $fields);

        // Extracting hits from Elasticsearch response
        $hits = $results['hits']['hits'] ?? [];

        return response()->json($hits);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'excerpt' => 'required',
            'description' => 'required',
            'image' => 'image|nullable',
            'keywords' => 'nullable',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $blogPost = BlogPost::create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

        // Dispatch job to queue for email notification
        dispatch(new \App\Jobs\SendPostPublishedNotification($blogPost));

        return response()->json($blogPost, 201);
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $this->authorize('update', $blogPost);

        $validated = $request->validate([
            'title' => 'required',
            'excerpt' => 'required',
            'description' => 'required',
            'image' => 'image|nullable',
            'keywords' => 'nullable',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($blogPost->image);
            $validated['image'] = $request->file('image')->store('images', 'public');
        }

        $blogPost->update($validated);
        return response()->json($blogPost, 200);
    }

    public function destroy(BlogPost $blogPost)
    {
        $this->authorize('delete', $blogPost);
        $blogPost->delete();
        return response()->json(null, 204);
    }
}
