<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->get();

        $posts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'user' => [
                    'id' => $post->user->id ?? null,
                    'name' => $post->user->name ?? 'Unknown',
                ],
                'comments' => $post->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => [
                            'id' => $comment->user->id ?? null,
                            'name' => $comment->user->name ?? 'Unknown',
                        ],
                    ];
                }),
                'created_at' => $post->created_at->toDateTimeString(),
            ];
        });

        return API_SUCCESS('Posts fetched successfully.', [
            'posts' => $posts,
        ]);
    }


    public function myPosts()
    {
        $posts = auth()->user()->posts()->with(['user', 'comments.user'])->latest()->get();

        return API_SUCCESS('success', [
            'post' => $posts
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return API_ERROR('Unauthorized', 401);
        }

        $validated = $this->RequestValidator($request);

        $post = Post::create([
            'user_id' => $user->id,
            'title'   => $validated['title'],
        ]);

        $post->load('user');

        return API_SUCCESS('Post created successfully', [
            'post' => $post
        ]);
    }




    public function show($id)
    {
        $user = auth()->user();
        $post = Post::with('user', 'comments.user')->findOrFail($user);

        return API_SUCCESS('This is the comments', [
            'post' => $post
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $this->RequestValidator($request, $id);

        $post->update($validated);

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    private function RequestValidator(Request $request, ?int $id = null)
    {
        $rules = [
            'title'   => $id ? 'sometimes|string|max:255' : 'required|string|max:255',
        ];

        $messages = [
            'title.required'   => 'The title field is required.',
            'title.string'     => 'The title must be text.',
            'title.max'        => 'The title may not be longer than 255 characters.',
        ];

        return $request->validate($rules, $messages);
    }
}
