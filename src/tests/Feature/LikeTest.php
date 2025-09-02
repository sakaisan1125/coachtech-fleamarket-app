<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_and_unlike_item_and_icon_changes()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        
        $this->actingAs($user);


        $item = Item::factory()->create(['name' => 'テスト商品']);

        $response = $this->get(route('items.show', $item));
        $response->assertOk();
        $response->assertSee('☆');
        $response->assertSee('like-btn');
        $response->assertSee('<span class="like-count">0</span>', false);

        $this->post(route('items.like', $item));
        $item->refresh();
        $user->refresh();

        $response = $this->get(route('items.show', $item));
        $response->assertOk();
        $response->assertSee('★');
        $response->assertSee('like-btn liked');
        $response->assertSee('<span class="like-count">1</span>', false);

        $this->delete(route('items.unlike', $item));
        $item->refresh();
        $user->refresh();

        $response = $this->get(route('items.show', $item));
        $response->assertOk();
        $response->assertSee('☆');
        $response->assertSee('like-btn');
        $response->assertSee('<span class="like-count">0</span>', false);
    }
}