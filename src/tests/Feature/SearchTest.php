<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** 商品名で部分一致検索ができる */
    public function test_can_search_items_by_partial_name(): void
    {
        Item::factory()->create(['name' => 'りんごジュース']);
        Item::factory()->create(['name' => 'みかんジュース']);
        Item::factory()->create(['name' => 'バナナ']);

        $response = $this->get(route('items.index', ['keyword' => 'ジュース']));
        $response->assertOk();
        $response->assertSee('りんごジュース')->assertSee('みかんジュース');
        $response->assertDontSee('バナナ');
    }

    /** 検索状態がマイリストでも保持されている */
    public function test_search_keyword_is_kept_on_mylist_tab(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $item = Item::factory()->create(['name' => 'オレンジジュース']);
        $user->likes()->create(['item_id' => $item->id]);

        // 検索キーワード付きでマイリストページにアクセス
        $response = $this->get(route('items.index', ['tab' => 'mylist', 'keyword' => 'ジュース']));
        $response->assertOk();
        $response->assertSee('オレンジジュース');
        // 検索欄にキーワードが保持されていることを確認
        $response->assertSee('value="ジュース"', false);
    }
}