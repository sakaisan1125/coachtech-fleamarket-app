<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** 「購入する」ボタンを押下すると購入が完了する */
    public function test_user_can_purchase_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this->actingAs($user)
            ->post(route('purchase.store', $item), [
                'address' => '東京都新宿区1-2-3',
                'payment_method' => 'card',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
        $item = $item->fresh();
        $this->assertTrue((bool)$item->is_sold);
    }

    /** 購入した商品は商品一覧画面にて「sold」と表示される */
    public function test_purchased_item_is_shown_as_sold_on_index()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '購入商品', 'is_sold' => false]);

        $this->actingAs($user)
            ->post(route('purchase.store', $item), [
                'address' => '東京都新宿区1-2-3',
                'payment_method' => 'card',
            ]);

        $response = $this->get(route('items.index'));
        $response->assertSee('購入商品');
        $response->assertSee('SOLD');
    }

    /** 購入した商品がプロフィールの購入商品一覧に追加されている */
    public function test_purchased_item_is_added_to_profile_list()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => 'プロフィール購入商品', 'is_sold' => false]);

        $this->actingAs($user)
            ->post(route('purchase.store', $item), [
                'address' => '東京都新宿区1-2-3',
                'payment_method' => 'card',
            ]);

        $response = $this->get('/mypage?page=buy');
        $response->assertSee('プロフィール購入商品');
    }
}