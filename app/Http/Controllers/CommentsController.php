<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentsController extends Controller
{

    public function index($postId)
    {
        $comments = Comment::with('user')
            ->where('post_id', $postId)
            ->latest()
            ->get();

        return API_SUCCESS('comment', [
            'comments' => $comments
        ]);
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|max:1000',
        ]);

        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $user->id,
            'comment' => $validated['comment'],
        ]);

        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'data' => ['comment' => $comment]
        ]);
    }


    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => ['comment' => $comment]
        ]);
    }


    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted'
        ]);
    }
}
