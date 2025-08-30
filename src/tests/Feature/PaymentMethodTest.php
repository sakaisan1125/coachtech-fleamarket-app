<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** 支払い方法選択画面に「支払い方法」ラベルが表示されることを確認 */
    public function test_payment_method_dropdown_reflects_to_summary_initial()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->get("/purchase/{$item->id}");

        $response->assertSee('支払い方法');
    }

    /** プルダウンに「コンビニ払い」「カード払い」の選択肢が表示されることを確認 */
    public function test_payment_method_options_are_displayed()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->get("/purchase/{$item->id}");

        $response->assertSee('コンビニ支払い');
        $response->assertSee('カード支払い');
    }

    /** 選択した支払い方法が購入履歴に正しく保存されることを確認 */
    public function test_selected_payment_method_is_saved()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);
        $response = $this->post("/purchase/{$item->id}", [
            'address' => '東京都新宿区1-2-3',
            'payment_method' => 'card',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => 'card',
        ]);
    }
}