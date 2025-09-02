<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(), // 投稿用のユーザーを自動作成
            'title'   => $this->faker->sentence(),    // ダミーの文章をタイトルに設定
            'body'    => $this->faker->paragraph(),   // ダミーの段落を本文に設定
        ];
    }
}