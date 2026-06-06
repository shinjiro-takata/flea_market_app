<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * ログインフォームを表示
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     * メール認証が未完了の場合は認証誘導画面へリダイレクト
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        if (!Auth::attempt($request->validated(), $request->boolean('remember'))) {
            // ログイン失敗時はエラーメッセージを表示
            return back()->withErrors([
                'email' => 'ログイン情報が登録されていません',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // メール認証が済んでいない場合は、認証誘導画面へ
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('verification.notice')->with('email', $user->email);
        }

        return redirect()->intended(route('items.index'));
    }

    /**
     * ログアウト処理
     */
    public function destroy(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('items.index');
    }
}
