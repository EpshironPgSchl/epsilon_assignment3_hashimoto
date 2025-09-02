<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーが掲示板にアクセスした場合、ログインページへリダイレクトされるテスト
     */
    public function test_guest_cannot_view_posts_and_is_redirected_to_login(): void
    {
        // 1. 実行 (Act)
        // 未ログイン状態で /posts にアクセスする
        $response = $this->get('/posts');

        // 2. 検証 (Assert)
        // /login ページにリダイレクトされたことを確認
        $response->assertRedirect('/login');
    }

    /**
     * ログイン済みユーザーが、投稿を作成できるテスト
     */
    public function test_authenticated_user_can_create_post(): void
    {
        // 1. 準備 (Arrange)
        $user = User::factory()->create();
        $postData = [
            'title' => 'テスト投稿のタイトル',
            'body' => 'これはテスト投稿の本文です。',
        ];

        // 2. 実行 (Act)
        // 作成したユーザーとしてログインし、/posts にPOSTリクエストを送信
        $response = $this->actingAs($user)->post('/posts', $postData);

        // 3. 検証 (Assert)
        // 検証1: 投稿後に /posts ページにリダイレクトされることを確認
        $response->assertRedirect('/posts');
        
        // 検証2: データベースの`posts`テーブルに、送信したデータが存在することを確認
        $this->assertDatabaseHas('posts', $postData);

    }
}