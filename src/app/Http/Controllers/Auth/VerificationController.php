<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * メール認証誘導画面を表示
     */
    public function notice(Request $request)
    {
        $user = $request->user();

        // 既に認証済みの場合は、ホーム画面へリダイレクト
        if ($user && $user->hasVerifiedEmail()) {
            return redirect()->route('items.index');
        }

        return view('auth.verify-email', ['email' => $user?->email ?? session('email')]);
    }

    /**
     * メール認証リンククリック時の処理
     * EmailVerificationRequest により、署名済みURLの検証が自動的に行われます
     */
    public function confirm(EmailVerificationRequest $request): RedirectResponse
    {
        return $this->completeEmailVerification($request->user());
    }

    /**
     * 「認証はこちらから」ボタンクリック時の処理
     */
    public function verifyNow(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 既に認証済みの場合
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('mypage.profile');
        }

        return $this->completeEmailVerification($user);
    }

    /**
     * メール認証メールを再送信
     */
    public function resend(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'ユーザーが見つかりません。']);
        }

        // 既に認証済みの場合
        if ($user->hasVerifiedEmail()) {
            return back()->with('message', 'メールは既に認証済みです。');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }

    /**
     * メール認証を完了する（共通処理）
     * 重複するメール認証ロジックをここに統一しました
     */
    private function completeEmailVerification(User $user): RedirectResponse
    {
        // メールを認証済みにマーク
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('mypage.profile')->with('verified', true);
    }
}
