@extends('layouts.auth')

@section('content')
<div class="register-wrap">
    <div style="text-align: center; margin-top: 80px;">
        {{-- メッセージ部分 --}}
        <div style="margin-bottom: 60px;">
            @if(Auth::user()->hasVerifiedEmail())
                <p style="font-size: 20px; color: #28a745; font-weight: bold;">
                    メール認証は完了しています！
                </p>
                <p style="font-size: 16px; color: #000;">
                    サービスをご利用いただけます。
                </p>
                <a href="{{ route('items.index') }}" style="
                    display: inline-block;
                    background-color: #35a5ff;
                    color: #fff;
                    padding: 15px 30px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-size: 16px;
                    font-weight: 500;
                    border: none;
                ">
                    商品一覧へ
                </a>
            @else
                <p style="font-size: 18px; margin-bottom: 5px; color: #000000ff; font-weight: 500;">
                    認証メール内のリンクをクリックすると認証が完了します。<br>
                    認証後はこの画面を更新してください。
                </p>
                <button onclick="location.reload();" style="
                    display: inline-block;
                    background-color: #d6d6d6;
                    color: #000000ff;
                    padding: 15px 30px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-size: 16px;
                    font-weight: 500;
                    border: none;
                    cursor: pointer;
                    margin-top: 50px;
                ">
                    状態を更新する
                </button>
            @endif
        </div>

        {{-- 成功・エラーメッセージ --}}
        @if (session('message'))
            <div style="color: #28a745; font-weight: bold; margin-bottom: 30px; font-size: 16px;">
                {{ session('message') }}
            </div>
        @endif

        @if (session('success'))
            <div style="color: #28a745; font-weight: bold; margin-bottom: 30px; font-size: 16px;">
                {{ session('success') }}
            </div>
        @endif

        @if(!Auth::user()->hasVerifiedEmail())
            {{-- 認証メール再送リンク --}}
            <div style="margin-bottom: 40px;">
                <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="
                        background: none;
                        border: none;
                        color: #35a5ff;
                        text-decoration: none;
                        cursor: pointer;
                        font-size: 16px;
                        padding: 0;
                    ">
                        認証メールを再送する
                    </button>
                </form>
            </div>
        @endif

        {{-- ログアウトリンク --}}
        <div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="
                    background: none;
                    border: none;
                    color: #666;
                    text-decoration: underline;
                    cursor: pointer;
                    font-size: 14px;
                    padding: 0;
                ">
                    ログアウト
                </button>
            </form>
        </div>
    </div>
</div>
@endsection