<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_exhibition_saves_all_fields()
    {
        $user = User::factory()->create();

        $category1 = Category::factory()->create(['name' => '家電']);
        $category2 = Category::factory()->create(['name' => '本']);

        $this->actingAs($user);

        $response = $this->post(route('items.store'), [
            'category_id' => [$category1->id, $category2->id],
            'condition' => '良好',
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明です。',
            'price' => 12345,
            'image' => \Illuminate\Http\UploadedFile::fake()->image('test.jpg'),
        ]);

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明です。',
            'condition' => '良好',
            'price' => 12345,
            'user_id' => $user->id,
        ]);

        $itemId = \App\Models\Item::where('name', 'テスト商品')->first()->id;

        $this->assertDatabaseHas('item_category', [
            'item_id' => $itemId,
            'category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('item_category', [
            'item_id' => $itemId,
            'category_id' => $category2->id,
        ]);
    }
}