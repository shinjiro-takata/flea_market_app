<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\PurchaseAddressRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Address;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ScreenController extends Controller
{
    /**
     * 商品一覧画面を表示する
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab') === 'mylist' ? 'mylist' : 'recommended';
        $q = $request->query('q') ?? '';

        $query = $this->buildItemQuery($tab, $q);
        $items = $query->get();
        $showLoginMessage = $tab === 'mylist' && !auth()->check();

        return view('items.index', [
            'items' => $items,
            'tab' => $tab,
            'showLoginMessage' => $showLoginMessage,
            'q' => $q,
        ]);
    }

    /**
     * 商品一覧クエリを構築
     *
     * @param string $tab 'mylist' または 'recommended'
     * @param string $q 検索キーワード
     */
    private function buildItemQuery(string $tab, string $q)
    {
        $query = Item::query()
            ->with(['seller', 'images'])
            ->withCount('likes')
            ->whereIn('status', ['on_sale', 'sold'])
            ->latest();

        // 自分が出品した商品は除外
        if (auth()->check()) {
            $query->where('seller_id', '!=', auth()->id());
        }

        // マイリストフィルタ
        if ($tab === 'mylist') {
            if (auth()->check()) {
                $query->whereHas('likes', function ($likeQuery) {
                    $likeQuery->where('user_id', auth()->id());
                });
            } else {
                // 未認証時は空結果
                $query->limit(0);
            }
        }

        // 商品名で検索
        if (!empty($q)) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        return $query;
    }

    /**
     * 商品詳細画面を表示する
     */
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

        $isLiked = auth()->check() && $item->likes()
            ->where('user_id', auth()->id())
            ->exists();

        return view('items.show', [
            'item' => $item,
            'isLiked' => $isLiked,
        ]);
    }

    /**
     * 送付先住所変更画面を表示する
     */
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

    /**
     * 購入時の送付先住所を更新する
     */
    public function updatePurchaseAddress(PurchaseAddressRequest $request, $itemId)
    {
        $user = auth()->user();

        // 既存の住所があれば更新、なければ作成
        $user->addresses()->firstOrCreate(
            [],
            $request->validated()
        )->update($request->validated());

        return redirect()->route('purchase.show', $itemId)->with('success', '住所を更新しました');
    }

    /**
     * 出品画面を表示する
     */
    public function sell()
    {
        $categories = Category::all();
        return view('items.sell', ['categories' => $categories]);
    }

    /**
     * 商品を出品して詳細画面へ遷移する
     */
    public function exhibition(ItemStoreRequest $request)
    {
        $validated = $request->validated();
        $userId = $this->resolveSellerId();

        $item = Item::create([
            'seller_id' => $userId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => 'on_sale',
            'brand_name' => $validated['brand_name'],
            'condition' => $validated['condition'],
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
        if (!empty($validated['categories'])) {
            $item->categories()->attach($validated['categories']);
        }

        return redirect()->route('items.show', $item->id)->with('success', '商品を出品しました');
    }

    /**
     * マイページを表示する
     */
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

    /**
     * プロフィール編集画面を表示する
     */
    public function profile()
    {
        $user = auth()->user();
        $address = $user->addresses()->first();

        return view('items.profile', [
            'user' => $user,
            'address' => $address,
        ]);
    }

    /**
     * プロフィール情報を更新する
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        // プロフィール画像の保存
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $validated['profile_image'] = $path;
        }

        // ユーザー情報の更新
        $user->update([
            'name' => $validated['name'],
            'profile_image' => $validated['profile_image'] ?? $user->profile_image,
        ]);

        // アドレス情報の更新または作成
        $user->addresses()->firstOrCreate(
            [],
            [
                'postal_code' => $validated['postal_code'],
                'prefecture' => $validated['prefecture'],
                'street_address' => $validated['street_address'] ?? '',
            ]
        )->update([
            'postal_code' => $validated['postal_code'],
            'prefecture' => $validated['prefecture'],
            'street_address' => $validated['street_address'] ?? '',
        ]);

        return redirect()->route('items.mypage')->with('success', 'プロフィールを更新しました');
    }

    /**
     * 出品者IDを決定する
     */
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

    /**
     * 会員登録を行ってログインさせる
     */
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
