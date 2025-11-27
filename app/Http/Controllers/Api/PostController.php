<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    // GET /api/posts
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $posts = Post::paginate($perPage);

        return PostResource::collection($posts);
    }

    // GET /api/posts/{id}
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    // POST /api/posts

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $post = Post::create($data);

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'user_id' => $post->user_id,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ], 201);
    }   


    // PATCH /api/posts/{id}
    public function update(PostRequest $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post->update($request->validated());

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => new PostResource($post),
        ], 200);
    }

    // DELETE /api/posts/{id}
    public function destroy(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }
}
