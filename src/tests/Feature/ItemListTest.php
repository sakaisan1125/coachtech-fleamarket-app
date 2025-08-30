<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Hash;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_items_are_displayed_on_item_list_page()
    {
        // 1. 商品ページを開くとすべての商品が表示される
        
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        Item::create([
            'name' => 'テスト商品1',
            'description' => 'テスト商品の説明',
            'price' => 1000,
            'condition' => '良好',
            'image_path' => 'test.jpg',
            'user_id' => $seller->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('テスト商品1');
    }

    /** @test */
    public function sold_items_display_sold_label()
    {
        // 2. 購入済みの商品にはSOLDのラベルが表示される
        
        $seller = User::create([
            'name' => '出品者',
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $buyer = User::create([
            'name' => '購入者',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $item = Item::create([
            'name' => '売り切れ商品',
            'description' => '売り切れ商品の説明',
            'price' => 2000,
            'condition' => '良好',
            'image_path' => 'sold.jpg',
            'user_id' => $seller->id,
            'is_sold' => true, // 売り切れ状態に設定
        ]);

        // 購入データ作成（整合性のため）
        Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('売り切れ商品');
        $response->assertSee('SOLD'); // SOLDラベルの確認
    }

    /** @test */
    public function logged_in_user_cannot_see_own_items()
    {
        // 3. ログインユーザーは自分の出品商品が表示されない
        
        $user = $this->createVerifiedUser([
            'name' => 'ログインユーザー',
            'email' => 'user@example.com',
        ]);

        $otherUser = $this->createVerifiedUser([
            'name' => '他のユーザー',
            'email' => 'other@example.com',
        ]);

        // 自分の商品
        Item::create([
            'name' => '自分の商品',
            'description' => '自分の商品の説明',
            'price' => 3000,
            'condition' => '良好',
            'image_path' => 'my_item.jpg',
            'user_id' => $user->id,
        ]);

        // 他人の商品
        Item::create([
            'name' => '他人の商品',
            'description' => '他人の商品の説明',
            'price' => 4000,
            'condition' => '良好',
            'image_path' => 'other_item.jpg',
            'user_id' => $otherUser->id,
        ]);

        // ログイン
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('自分の商品'); // 自分の商品は非表示
        $response->assertSee('他人の商品');     // 他人の商品は表示
    }

    /** @test */
    public function guest_user_can_see_all_items()
    {
        // 4. 未ログインユーザーは全ての商品が表示される
        
        $user1 = $this->createVerifiedUser([
            'name' => 'ユーザー1',
            'email' => 'user1@example.com',
        ]);

        $user2 = $this->createVerifiedUser([
            'name' => 'ユーザー2',
            'email' => 'user2@example.com',
        ]);

        // 各ユーザーの商品を作成
        Item::create([
            'name' => 'ユーザー1の商品',
            'description' => 'ユーザー1の商品説明',
            'price' => 5000,
            'condition' => '良好',
            'image_path' => 'user1_item.jpg',
            'user_id' => $user1->id,
        ]);

        Item::create([
            'name' => 'ユーザー2の商品',
            'description' => 'ユーザー2の商品説明',
            'price' => 6000,
            'condition' => '良好',
            'image_path' => 'user2_item.jpg',
            'user_id' => $user2->id,
        ]);

        // 未ログイン状態でアクセス
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1の商品');
        $response->assertSee('ユーザー2の商品');
    }

    /**
     * メール認証済みユーザーを作成するヘルパーメソッド
     */
    private function createVerifiedUser(array $attributes = []): User
    {
        $user = User::create(array_merge([
            'password' => Hash::make('password123'),
        ], $attributes));

        // メール認証を確実に設定
        $user->email_verified_at = now();
        $user->save();
        $user->refresh();

        return $user;
    }
}