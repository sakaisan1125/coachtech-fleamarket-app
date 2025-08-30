<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** いいね機能のテスト：いいね追加・解除・アイコン色変化 */
    public function test_user_can_like_and_unlike_item_and_icon_changes()
    {
        // 1. ユーザー作成＆ログイン
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        // 2. 商品作成
        $item = Item::factory()->create(['name' => 'テスト商品']);

        // 3. 商品詳細ページを開く（初期状態：いいねなし）
        $response = $this->get(route('items.show', $item));
        $response->assertOk();
        $response->assertSee('☆'); // いいね前は白抜きハート
        $response->assertSee('like-btn'); // アイコンのクラス
        $response->assertSee('<span class="like-count">0</span>', false);

        // 4. いいねアイコンを押下（POSTリクエスト）
        $this->post(route('items.like', $item));
        $item->refresh();
        $user->refresh(); // ← 追加推奨

        // 5. 商品詳細ページを再度開く（いいね済み状態）
        $response = $this->get(route('items.show', $item));
        $response->assertOk();
        $response->assertSee('★'); // いいね済みは黒塗りハート
        $response->assertSee('like-btn liked'); // アイコンの色変化
        $response->assertSee('<span class="like-count">1</span>', false);

        // 6. いいね解除（DELETEリクエスト）
        $this->delete(route('items.unlike', $item));
        $item->refresh();
        $user->refresh(); // ← 追加推奨

        // 7. 商品詳細ページを再度開く（いいね解除状態）
        $response = $this->get(route('items.show', $item));
        $response->assertOk();
        $response->assertSee('☆'); // 再び白抜きハート
        $response->assertSee('like-btn'); // アイコンのクラス
        $response->assertSee('<span class="like-count">0</span>', false);
    }
}