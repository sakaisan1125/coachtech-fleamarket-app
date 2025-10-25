<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedUser(): User
    {
        $user = User::factory()->create();
        $user->markEmailAsVerified();
        return $user;
    }

    public function test_guest_mylist_is_empty_but_200(): void
    {
        $otherUser = User::factory()->create();
        $likedItem = Item::factory()->create(['name' => 'LIKED-BY-OTHER']);
        $otherUser->likes()->create(['item_id' => $likedItem->id]);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertOk()
            ->assertDontSee('LIKED-BY-OTHER');
    }

    public function test_verified_user_sees_only_liked_items_excluding_self(): void
    {
        $user = $this->createVerifiedUser();
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        $likedItems = Item::factory()->count(2)->create(['user_id' => $otherUser->id, 'name' => 'LIKED-ITEM-1']);
        $likedItems[1]->update(['name' => 'LIKED-ITEM-2']);

        $otherItems = collect([
            Item::factory()->create(['user_id' => $otherUser->id, 'name' => 'OTHER-ITEM-1']),
            Item::factory()->create(['user_id' => $otherUser->id, 'name' => 'OTHER-ITEM-2']),
        ]);

        $myItem = Item::factory()->create(['user_id' => $user->id, 'name' => 'MY-ITEM']);

        foreach ($likedItems as $item) {
            $user->likes()->create(['item_id' => $item->id]);
        }
        $user->likes()->create(['item_id' => $myItem->id]);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertOk();
        $response->assertSee('LIKED-ITEM-1')->assertSee('LIKED-ITEM-2');
        $response->assertDontSee('OTHER-ITEM-1')->assertDontSee('OTHER-ITEM-2');
        $response->assertDontSee('MY-ITEM');
    }

    public function test_unverified_logged_in_is_redirected_to_verify(): void
    {
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($unverifiedUser);

        $this->get(route('items.index', ['tab' => 'mylist']))
            ->assertRedirect('/email/verify');
    }

    public function test_sold_items_show_sold_label(): void
    {
        $user = $this->createVerifiedUser();
        $this->actingAs($user);

        $seller = User::factory()->create();
        $soldItem = Item::factory()->create([
            'name' => 'SOLD-ITEM',
            'user_id' => $seller->id,
        ]);

        $user->likes()->create(['item_id' => $soldItem->id]);

        $soldItem->update(['is_sold' => true]);

        DB::table('purchases')->insert([
            'item_id'        => $soldItem->id,
            'user_id'        => User::factory()->create()->id,
            'seller_id'      => $seller->id,
            'address'        => '和歌山市1-2-3',
            'payment_method' => 'card',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $this->get(route('items.index', ['tab' => 'mylist']))
            ->assertOk()
            ->assertSee('SOLD');
    }
}