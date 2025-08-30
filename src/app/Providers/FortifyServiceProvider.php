<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Responses\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // この行を追加
        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            \App\Http\Responses\RegisterResponse::class
        );
        //このコードは**「サービスコンテナへの登録（バインド）」**と呼ばれるLaravelの仕組みを使っています。
        //超簡単な日本語でまとめると…「新規登録が終わったときに使うレスポンスの中身は、“自分で作ったRegisterResponse”を使って！」「しかも1個だけ（シングルトン）を全体で使いまわしてね」
        //Laravel Fortifyは「登録後のリダイレクト先」を自分でコントロールしたい人向けにRegisterResponseContract という「決まり（契約）」を用意してる普通は /home にリダイレクト（デフォルト）自分でRegisterResponseクラスを作って /profile/edit へ変えたい場合は、このバインドで差し替え！
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
        return view('auth.login');
        });

        Fortify::registerView(function () {
        return view('auth.register');
        });

        Fortify::verifyEmailView('auth.verify-email');

        Fortify::authenticateUsing(function ($request) {
        try {
            app(LoginRequest::class)->validateResolved();
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            // バリデーションエラー時は自動でリダイレクト＆エラー表示
            throw $validationException;
        }

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return $user;
        }
        // ここでエラーメッセージを返す
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
        });
        }

        // use App\Http\Requests\LoginRequest;
        //`LoginRequest`（あなたが作ったバリデーションルールを含むクラス）をこのファイルで使えるようにする。
        // - ファイルの上部に書くことで、`LoginRequest::class` を `app()` などで呼び出せる。
        // use Illuminate\Support\Facades\Hash;
        // パスワードを比較するための Hash::check() を使う準備。
        // Laravel ではパスワードがハッシュ化されているので、ログイン時にこれを使って照合する。
        // use App\Models\User;
        // `users` テーブルに対応する Eloquent モデル。
        // ユーザーのレコードを取得するために使う。
        // Fortify::authenticateUsing(function ($request) {
        // Fortify に「ログイン処理はこの関数でやってね」と教えている。
        // $request はログインフォームから送られてきた入力（email, password）を含むリクエスト。
        // $request はログインフォームから送られてきた入力（email, password）を含むリクエスト。
        // app(LoginRequest::class)->validateResolved(); `LoginRequest.php` の **バリデーションルールをここで手動実行**している。
        //  `validateResolved()` は「このリクエストのルールでバリデーションを今すぐ実行してエラーなら止める」という意味。
        //  $user = User::where('email', $request->email)->first();
        // users テーブルから、入力されたメールアドレスに一致するユーザーを1件取得。
        // もし該当するユーザーがいなければ $user は null になる
        // if ($user && Hash::check($request->password, $user->password)) {
        // `$user` が存在し、かつパスワードが一致していれば `true`。
        // `Hash::check()` は「平文のパスワード」と「ハッシュ化されたDBのパスワード」を比較して、一致すれば `true`。
        // return $user;
        // 上の条件を満たした（＝ログイン成功）ので、認証成功としてユーザー情報を返す。
        // Fortify はこの User オブジェクトを使って自動的にログイン状態にする。
        // return null;
        // 条件を満たさなかった（メールが見つからない、またはパスワードが違う）のでログイン失敗として null を返す。
        // Fortify はこの null を見て、「ログイン失敗」と判断する。
}
