<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 論理削除されたユーザーの投稿が非表示になることを確認するテスト
     *
     * @return void
     */
    public function test_posts_from_soft_deleted_user_are_hidden(): void
    {
        // 1. 準備 (Arrange)
        $activeUser = User::factory()->create();
        $deletedUser = User::factory()->create();

        $activeUserPost = Post::factory()->create(['user_id' => $activeUser->id]);
        $deletedUserPost = Post::factory()->create(['user_id' => $deletedUser->id]);

        // 2. 実行 (Act)
        $deletedUser->delete();

        // 3. 検証 (Assert)
        
        // ▼▼▼ ここに検証を追加 ▼▼▼
        // 検証1: ユーザーが物理削除ではなく、論理削除されていることを確認
        $this->assertSoftDeleted($deletedUser);
        
        // PostControllerと同じロジックで投稿を取得
        $visiblePosts = Post::whereHas('user')->get();

        // 検証2: 取得できる投稿が1件であることを確認
        $this->assertCount(1, $visiblePosts);

        // 検証3: 取得できた投稿が、アクティブなユーザーのものであることを確認
        $this->assertEquals($activeUser->id, $visiblePosts->first()->user_id);
    }
}