<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * パスワードが作成時にハッシュ化されることを確認するテスト
     *
     * @return void
     */
    public function test_password_is_hashed_on_creation(): void
    {
        // 1. 準備 (Arrange)
        // 'password' という平文パスワードでユーザーを作成
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        // 2. 実行 (Act) & 3. 検証 (Assert)
        
        // 検証1: DBに保存されたパスワードが平文でないことを確認
        $this->assertNotEquals('password123', $user->password);

        // 検証2: 平文のパスワードと、DBに保存されたハッシュ化済みパスワードが一致するかを確認
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}