<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Item;

class LikeController extends Controller
{
    public function toggle($itemId)
    {
        $user = auth()->user();
        $item = Item::find($itemId);

        $like = Like::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            Like::create([
                'user_id' => $user->id,
                'item_id' => $itemId,
            ]);
        }

        return redirect()->back();
    }
}
