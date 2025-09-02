<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_form_shows_initial_values()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'name' => '初期ユーザー名',
            'profile_image' => UploadedFile::fake()->image('avatar.jpg')->store('profiles', 'public'),
            'zipcode' => '123-4567',
            'address' => '東京都新宿区1-2-3',
            'building' => '初期マンション',
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage/profile');

        $response->assertSee('初期ユーザー名');
        $response->assertSee('123-4567');
        $response->assertSee('東京都新宿区1-2-3');
        $response->assertSee('初期マンション');
        $response->assertSee(Storage::url($user->profile_image));
    }
}