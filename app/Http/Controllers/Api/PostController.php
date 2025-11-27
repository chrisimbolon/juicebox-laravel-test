<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    // GET /api/posts
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $posts = Post::with('user')->paginate($perPage);
        return response()->json($posts);
    }

    // GET /api/posts/{id}
    public function show(Post $post)
    {
        $post->load('user');
        return response()->json($post);
    }

    // POST /api/posts
    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $post = Post::create($data);
        return response()->json($post, 201);
    }

    // PATCH /api/posts/{id}
    public function update(PostRequest $request, Post $post)
    {
        // Check ownership
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $post->update($request->validated());
        return response()->json($post);
    }

    // DELETE /api/posts/{id}
    public function destroy(Request $request, Post $post)
    {
        // Check ownership
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $post->delete();
        return response()->json(null, 204);
    }
}