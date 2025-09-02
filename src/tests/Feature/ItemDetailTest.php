<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_detail_page_shows_all_information(): void
    {
        $user = User::factory()->create();

        $category = Category::factory()->create(['name' => '家電']);

        $item = Item::factory()->create([
            'name'        => '冷蔵庫',
            'brand'       => 'Panasonic',
            'price'       => 50000,
            'description' => '大容量の冷蔵庫です。',
            'condition'   => '新品',
            'image_path'  => 'dummy.jpg',
        ]);

        $item->categories()->attach($category->id);

        $user->likes()->create(['item_id' => $item->id]);

        $commentUser = User::factory()->create(['name' => 'コメント太郎']);

        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => 'とても良い商品ですね！',
        ]);

        $response = $this->get(route('items.show', ['id' => $item->id]));

        $response->assertOk();
        $response->assertSee('dummy.jpg');
        $response->assertSee('冷蔵庫');
        $response->assertSee('Panasonic');
        $response->assertSee('￥50,000');
        $response->assertSee('1');
        $response->assertSee('1');
        $response->assertSee('大容量の冷蔵庫です。');
        $response->assertSee('家電');
        $response->assertSee('新品');
        $response->assertSee('コメント太郎');
        $response->assertSee('とても良い商品ですね！');
    }

    public function test_item_detail_page_shows_multiple_categories(): void
    {
        $user = User::factory()->create();

        $category1 = Category::factory()->create(['name' => '家電']);
        $category2 = Category::factory()->create(['name' => '生活雑貨']);

        $item = Item::factory()->create([
            'name'        => '冷蔵庫',
            'brand'       => 'Panasonic',
            'price'       => 50000,
            'description' => '大容量の冷蔵庫です。',
            'condition'   => '新品',
            'image_path'  => 'dummy.jpg',
        ]);

        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get(route('items.show', ['id' => $item->id]));

        $response->assertOk();
        $response->assertSee('家電');
        $response->assertSee('生活雑貨');
    }
}