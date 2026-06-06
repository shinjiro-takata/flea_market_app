<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Item;

class LikeController extends Controller
{
    public function toggle($itemId)
    {
        $userId = auth()->id();
        $like = Like::firstWhere(['user_id' => $userId, 'item_id' => $itemId]);

        if ($like) {
            $like->delete();
        } else {
            Like::create([
                'user_id' => $userId,
                'item_id' => $itemId,
            ]);
        }

        return redirect()->back();
    }
}
