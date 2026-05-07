<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Address;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ScreenController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab') === 'mylist' ? 'mylist' : 'recommended';
        $q = $request->query('q') ?? '';

        $query = Item::query()
            ->with(['seller', 'images'])
            ->withCount('likes')
            ->latest();

        // 自分が出品した商品は除外
        if (auth()->check()) {
            $query->where('seller_id', '!=', auth()->id());
        }

        $showLoginMessage = false;

        if ($tab === 'mylist') {
            if (auth()->check()) {
                $query->whereHas('likes', function ($likeQuery) {
                    $likeQuery->where('user_id', auth()->id());
                });
            } else {
                $showLoginMessage = true;
                $query->whereRaw('1 = 0');
            }
        }

        // 商品名で検索
        if (!empty($q)) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        $items = $query->get();

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
            'showLoginMessage' => $showLoginMessage,
            'q' => $q,
        ]);
    }

    public function showItem($itemId)
    {
        $item = Item::query()
            ->with([
                'seller',
                'images' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('id');
                },
                'comments' => function ($query) {
                    $query->with('user')->latest();
                },
            ])
            ->withCount(['likes', 'comments'])
            ->findOrFail($itemId);

        $isLiked = false;

        if (auth()->check()) {
            $isLiked = $item->likes()
                ->where('user_id', auth()->id())
                ->exists();
        }

        return view('items.show', [
            'item' => $item,
            'isLiked' => $isLiked,
        ]);
    }

    public function purchase($itemId)
    {
        return view('pages.screen', [
            'title' => '商品購入画面',
            'path' => '/purchase/{item_id}',
            'description' => '支払い方法の選択と購入確認を行う画面です。',
            'state' => [
                'item_id' => $itemId,
            ],
        ]);
    }

    public function purchaseAddress($itemId)
    {
        $item = Item::find($itemId);
        $user = auth()->user();
        $address = $user->addresses()->first();

        return view('purchase.address', [
            'item' => $item,
            'address' => $address,
        ]);
    }

    public function updatePurchaseAddress(Request $request, $itemId)
    {
        $user = auth()->user();
        $address = $user->addresses()->first();

        if ($address) {
            $address->update([
                'postal_code' => $request->input('postal_code'),
                'prefecture' => $request->input('prefecture'),
                'municipality' => $request->input('municipality'),
                'street_address' => $request->input('street_address'),
            ]);
        } else {
            Address::create([
                'user_id' => $user->id,
                'postal_code' => $request->input('postal_code'),
                'prefecture' => $request->input('prefecture'),
                'municipality' => $request->input('municipality'),
                'street_address' => $request->input('street_address'),
            ]);
        }

        return redirect()->route('purchase.show', $itemId)->with('success', '住所を更新しました');
    }

    public function sell()
    {
        $categories = Category::all();
        return view('items.sell', ['categories' => $categories]);
    }

    public function exhibition(ExhibitionRequest $request)
    {
        $userId = $this->resolveSellerId();

        $item = Item::create([
            'seller_id' => $userId,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'status' => 'on_sale',
            'brand_name' => $request->brand_name,
            'condition' => $request->condition,
        ]);

        // 画像保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => $path,
            ]);
        }

        // カテゴリをアタッチ
        $item->categories()->attach($request->categories);

        return redirect()->route('items.show', $item->id);
    }

    public function mypage(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 'sell');
        $sellItems = $user->items()->with('images')->latest()->get();
        $buyItems = $user->purchaseOrders()->with('item.images')->latest()->get();

        return view('items.mypage', [
            'user' => $user,
            'page' => $page,
            'sellItems' => $sellItems,
            'buyItems' => $buyItems,
        ]);
    }

    public function profile()
    {
        $user = auth()->user();
        $address = $user->addresses()->first();

        return view('items.profile', [
            'user' => $user,
            'address' => $address,
        ]);
    }

    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        // プロフィール画像の保存
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->update(['profile_image' => $path]);
        }

        // ユーザー情報の更新
        $user->update([
            'name' => $request->input('name'),
        ]);

        // アドレス情報の更新または作成
        $address = $user->addresses()->first();

        if ($address) {
            $address->update([
                'postal_code' => $request->input('postal_code'),
                'prefecture' => $request->input('prefecture'),
                'municipality' => $request->input('municipality'),
                'street_address' => $request->input('street_address'),
            ]);
        } else {
            \App\Models\Address::create([
                'user_id' => $user->id,
                'postal_code' => $request->input('postal_code'),
                'prefecture' => $request->input('prefecture'),
                'municipality' => $request->input('municipality'),
                'street_address' => $request->input('street_address'),
            ]);
        }

        return redirect()->route('items.mypage')->with('success', 'プロフィールを更新しました');
    }

    private function resolveSellerId()
    {
        if (auth()->check()) {
            return auth()->id();
        }

        $existingUserId = User::query()->value('id');

        if ($existingUserId) {
            return $existingUserId;
        }

        $seller = User::firstOrCreate(
            ['email' => 'sample_seller@example.com'],
            [
                'name' => 'サンプル出品者',
                'password' => Hash::make('password'),
            ]
        );

        return $seller->id;
    }

    public function storeUser(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        auth()->login($user);

        return redirect()->route('items.index');
    }
}
