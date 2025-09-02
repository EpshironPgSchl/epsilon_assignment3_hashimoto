<?php

namespace App\Http\Controllers;

use App\Models\Post; // ⬅️ Postモデルをインポートしているか
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ⬅️ Authをインポートしているか

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Post $post)
    {
        // バリデーション
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        // Postモデルとリレーションを使ってコメントを作成
        $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        // 元のページに戻る
        return back()->with('message', 'コメントを投稿しました。');
    }
}