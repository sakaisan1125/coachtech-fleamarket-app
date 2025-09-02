<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_changed_address_is_reflected_on_purchase_screen()
    {
        $user = User::factory()->create([
            'zipcode' => '100-0001',
            'address' => '東京都千代田区1-1-1',
            'building' => '旧住所ビル',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post(route('address.update'), [
            'zipcode' => '150-0001',
            'address' => '北海道札幌市2-2-2',
            'building' => '新住所マンション',
        ]);

        $user->refresh();

        $response = $this->get("/purchase/{$item->id}");
        $response->assertSee('150-0001');
        $response->assertSee('北海道札幌市2-2-2');
        $response->assertSee('新住所マンション');
    }

    public function test_purchased_item_has_correct_address()
    {
        $user = User::factory()->create([
            'zipcode' => '100-0001',
            'address' => '東京都千代田区1-1-1',
            'building' => '旧住所ビル',
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user);

        $this->post('/address/update', [
            'zipcode' => '150-0001',
            'address' => '北海道札幌市2-2-2',
            'building' => '新住所マンション',
        ]);

        $this->post("/purchase/{$item->id}", [
            'address' => '北海道札幌市2-2-2',
            'payment_method' => 'card',
        ]);

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'address' => '北海道札幌市2-2-2',
        ]);
    }
}