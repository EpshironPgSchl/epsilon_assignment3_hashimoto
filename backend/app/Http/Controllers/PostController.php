<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ⬅️ 追加

class PostController extends Controller
{
    use AuthorizesRequests; // ⬅️ 追加

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Posts/Index', [
            'posts' => Post::with([
                'user:id,name',
                'comments' => function ($query) {
                    $query->whereHas('user')->with('user:id,name');
                }
            ])
                ->whereHas('user')
                ->latest()
                ->paginate(10),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:1000',
        ]);

        $request->user()->posts()->create($validated);

        return to_route('posts.index')->with('message', '投稿しました。');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        // 認可チェック
        $this->authorize('update', $post);

        // バリデーション
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:1000',
        ]);

        // 更新処理
        $post->update($validated);

        return to_route('posts.index')->with('message', '投稿を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // 認可チェック
        $this->authorize('delete', $post);

        // 削除処理
        $post->delete();
        
        return to_route('posts.index')->with('message', '投稿を削除しました。');
    }
}