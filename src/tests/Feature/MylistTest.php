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

    /** ① 未ログインでも /?tab=mylist は 200 で空表示 */
    public function test_guest_mylist_is_empty_but_200(): void
    {
        $otherUser = User::factory()->create();
        $likedItem = Item::factory()->create(['name' => 'LIKED-BY-OTHER']);
        $otherUser->likes()->create(['item_id' => $likedItem->id]);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertOk()
            ->assertDontSee('LIKED-BY-OTHER');
    }

    /** ② 認証済みユーザー：いいねした商品“だけ”表示（自分の出品は除外） */
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

    /** ③ ログイン済みだがメール未認証 → /email/verify へリダイレクト */
    public function test_unverified_logged_in_is_redirected_to_verify(): void
    {
        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $this->actingAs($unverifiedUser);

        $this->get(route('items.index', ['tab' => 'mylist']))
            ->assertRedirect('/email/verify');
    }

    /** ④ 購入済みは「Sold」表示（purchase or is_sold どちらでも通るよう両対応） */
    public function test_sold_items_show_sold_label(): void
    {
        $user = $this->createVerifiedUser();
        $this->actingAs($user);

        $soldItem = Item::factory()->create(['name' => 'SOLD-ITEM']);
        $user->likes()->create(['item_id' => $soldItem->id]);

        $soldItem->update(['is_sold' => true]);

        DB::table('purchases')->insert([
            'item_id'        => $soldItem->id,
            'user_id'        => User::factory()->create()->id,
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