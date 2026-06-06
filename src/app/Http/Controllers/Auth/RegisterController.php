<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * 会員登録フォームを表示
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * 新規会員登録を処理
     * 1. ユーザーデータを検証して作成
     * 2. Registered イベントを発火（メール認証メール送信）
     * 3. ユーザーを自動ログイン
     * 4. メール認証誘導画面へリダイレクト
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();

        // ユーザーを新規作成
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // メール認証メール送信イベント発火
        event(new Registered($user));

        // ユーザーを自動ログイン
        auth()->login($user);

        // メール認証誘導画面へリダイレクト
        return redirect()->route('verification.notice');
    }
}
