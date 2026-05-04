<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $itemId)
    {
        $user = auth()->user();
        Comment::create([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back();
    }
}
