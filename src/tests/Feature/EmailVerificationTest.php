<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * 1. 会員登録後、認証メールが送信される 
     */
    public function test_register_sends_verification_email()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** 
     * 2. メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する 
     */
    public function test_verify_email_notice_redirects_to_verification_site()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        $response = $this->get('/email/verify');
        $response->assertStatus(200);
        $response->assertSee('認証はこちらから');
        $response->assertSee(route('email.verified'));

        // 「認証はこちらから」ボタンのリンク先にアクセス（遷移をシミュレート）
        $redirectResponse = $this->get(route('email.verified'));
        // 認証サイト（auth.verifiedビュー）が表示されることを確認
        $redirectResponse->assertStatus(200);
        $redirectResponse->assertSee('認証メール内のリンクをクリックすると認証が完了します。'); // ビューに応じて文言を調整
    }

    /** 
     * 3. メール認証サイトのメール認証を完了すると、商品一覧ページに遷移する 
     */

    public function test_verified_email_redirects_to_items_index()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        // メール認証リンク生成
        $verificationUrl = URL::signedRoute('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $response = $this->get($verificationUrl);

        // 認証後にリダイレクトされることを確認
        $response->assertRedirect('/');

        // ユーザーが認証済みになっていることも確認
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }
}