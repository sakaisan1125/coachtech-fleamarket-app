<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** プロフィール編集画面で各項目の初期値が正しく表示されることを確認 */
    public function test_profile_edit_form_shows_initial_values()
    {
        Storage::fake('public');

        // ユーザー作成（プロフィール画像付き）
        $user = User::factory()->create([
            'name' => '初期ユーザー名',
            'profile_image' => UploadedFile::fake()->image('avatar.jpg')->store('profiles', 'public'),
            'zipcode' => '123-4567',
            'address' => '東京都新宿区1-2-3',
            'building' => '初期マンション',
        ]);

        $this->actingAs($user);

        // プロフィール編集ページ表示
        $response = $this->get('/mypage/profile');

        // 各項目の初期値が表示されていることを確認
        $response->assertSee('初期ユーザー名');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区1-2-3');
        $response->assertSee('初期マンション');
        $response->assertSee(Storage::url($user->profile_image));
    }
}