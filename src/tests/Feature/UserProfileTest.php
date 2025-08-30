<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** 必要な情報がプロフィールページで取得できることを確認 */
    public function test_user_profile_information_is_displayed()
    {
        Storage::fake('public');

        // ユーザー作成（プロフィール画像付き）
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => UploadedFile::fake()->image('avatar.jpg')->store('profiles', 'public'),
            'address' => '北海道札幌市1-2-3', // 住所を追加
        ]);

        // 出品商品2件
        $sellItem1 = Item::factory()->create(['user_id' => $user->id, 'name' => '出品商品A']);
        $sellItem2 = Item::factory()->create(['user_id' => $user->id, 'name' => '出品商品B']);

        // 購入商品2件
        $buyItem1 = Item::factory()->create(['name' => '購入商品A']);
        $buyItem2 = Item::factory()->create(['name' => '購入商品B']);
        // 購入履歴を紐付け
        $user->purchases()->create(['item_id' => $buyItem1->id, 'address' => $user->address, 'payment_method' => 'card']);
        $user->purchases()->create(['item_id' => $buyItem2->id, 'address' => $user->address, 'payment_method' => 'card']);

        $this->actingAs($user);

        // プロフィールページ表示
        $response = $this->get('/mypage');
        $response->assertSee('テストユーザー');
        $response->assertSee('出品商品A');
        $response->assertSee('出品商品B');
        // プロフィール画像のパスがHTMLに含まれているか
        $response->assertSee(Storage::url($user->profile_image));

        // マイページ（購入商品一覧タブ）表示
        $responseBuy = $this->get('/mypage?page=buy');
        $responseBuy->assertSee('購入商品A');
        $responseBuy->assertSee('購入商品B');
    }
}