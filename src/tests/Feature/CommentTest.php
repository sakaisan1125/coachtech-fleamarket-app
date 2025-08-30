<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** ログイン済みのユーザーはコメントを送信できる */
    public function test_authenticated_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'content' => 'テストコメント',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => 'テストコメント',
        ]);
    }

    /** ログイン前のユーザーはコメントを送信できない */
    public function test_guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comments.store', $item), [
            'content' => 'ゲストコメント',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('comments', [
            'content' => 'ゲストコメント',
        ]);
    }

    /** コメントが255字以上の場合、バリデーションメッセージが表示される */
    public function test_comment_over_255_characters_shows_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'content' => $longComment,
            ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('comments', [
            'content' => $longComment,
        ]);
    }

    /** コメントが入力されていない場合、バリデーションメッセージが表示される */
    public function test_empty_comment_shows_validation_error()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item), [
                'content' => '',
            ]);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseMissing('comments', [
            'content' => '',
        ]);
    }
}