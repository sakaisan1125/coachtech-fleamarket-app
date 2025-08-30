<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /** 送付先住所変更画面で登録した住所が商品購入画面に反映される */
    public function test_changed_address_is_reflected_on_purchase_screen()
    {
        $user = User::factory()->create([
            'zipcode' => '100-0001',
            'address' => '東京都千代田区1-1-1',
            'building' => '旧住所ビル',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 住所変更フォーム送信
        $this->post(route('address.update'), [
            'zipcode' => '150-0001',
            'address' => '北海道札幌市2-2-2',
            'building' => '新住所マンション',
        ]);
        $user->refresh(); 

        // 商品購入画面を再度開く
        $response = $this->get("/purchase/{$item->id}");

        // 新しい住所が表示されていることを確認
        $response->assertSee('150-0001');
        $response->assertSee('北海道札幌市2-2-2');
        $response->assertSee('新住所マンション');
    }

    /** 購入した商品に送付先住所が紐づいて登録される */
    public function test_purchased_item_has_correct_address()
    {
        $user = User::factory()->create([
            'zipcode' => '100-0001',
            'address' => '東京都千代田区1-1-1',
            'building' => '旧住所ビル',
        ]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        // 住所変更
        $this->post('/address/update', [
            'zipcode' => '150-0001',
            'address' => '北海道札幌市2-2-2',
            'building' => '新住所マンション',
        ]);

        // 商品購入
        $this->post("/purchase/{$item->id}", [
            'address' => '北海道札幌市2-2-2',
            'payment_method' => 'card',
        ]);

        // purchasesテーブルに新住所が紐づいていることを確認
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'address' => '北海道札幌市2-2-2',
        ]);
    }
}