<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm , usePage } from '@inertiajs/vue3';
import { defineProps, reactive, ref, computed } from 'vue';

const props = defineProps({
    posts: Object,
});

// ログイン中のユーザー情報を取得
const user = computed(() => usePage().props.auth.user);

// --- 投稿フォームのロジック ---
const postForm = useForm({
    title: '',
    body: '',
});

const submitPost = () => {
    postForm.post(route('posts.store'), {
        onSuccess: () => postForm.reset(),
    });
};

// --- コメントフォームのロジック (リアクティブなオブジェクトで管理) ---
const commentForms = reactive({});

const initializeCommentForm = (postId) => {
    if (!commentForms[postId]) {
        commentForms[postId] = useForm({
            body: '',
        });
    }
};

const submitComment = (postId) => {
    const form = commentForms[postId];
    if (form) {
        form.post(route('comments.store', { post: postId }), {
            onSuccess: () => form.reset('body'),
            preserveScroll: true,
            preserveState: false,
        });
    }
};

// --- ログイン中のユーザー情報を取得 ---
const authUser = computed(() => usePage().props.auth.user);

// --- 編集モーダルのロジック ---
const editingPost = ref(null);
const editForm = useForm({
    title: '',
    body: '',
});
const openEditModal = (post) => {
    editingPost.value = post;
    editForm.title = post.title;
    editForm.body = post.body;
};
const updatePost = () => {
    if (!editingPost.value) return;
    editForm.put(route('posts.update', editingPost.value.id), {
        onSuccess: () => (editingPost.value = null),
    });
};

// --- 削除ロジック ---
const deletePost = (postId) => {
    if (confirm('本当にこの投稿を削除しますか？')) {
        useForm({}).delete(route('posts.destroy', postId), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="掲示板" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">掲示板</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">新しい投稿</h3>
                        <form @submit.prevent="submitPost">
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700">タイトル</label>
                                <input v-model="postForm.title" id="title" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required />
                                <div v-if="postForm.errors.title" class="text-sm text-red-600 mt-1">{{ postForm.errors.title }}</div>
                            </div>
                            <div class="mb-4">
                                <label for="body" class="block text-sm font-medium text-gray-700">本文</label>
                                <textarea v-model="postForm.body" id="body" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                                <div v-if="postForm.errors.body" class="text-sm text-red-600 mt-1">{{ postForm.errors.body }}</div>
                            </div>
                            <button type="submit" :disabled="postForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:bg-indigo-300">
                                投稿する
                            </button>
                            <div v-if="editingPost" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
        <h3 class="text-lg font-bold mb-4">投稿を編集する</h3>
        <form @submit.prevent="updatePost">
            <div class="mb-4">
                <label for="edit-title">タイトル</label>
                <input v-model="editForm.title" id="edit-title" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                <div v-if="editForm.errors.title" class="text-sm text-red-600 mt-1">{{ editForm.errors.title }}</div>
            </div>
            <div class="mb-4">
                <label for="edit-body">本文</label>
                <textarea v-model="editForm.body" id="edit-body" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                <div v-if="editForm.errors.body" class="text-sm text-red-600 mt-1">{{ editForm.errors.body }}</div>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" @click="editingPost = null" class="px-4 py-2 bg-gray-200 rounded-md">キャンセル</button>
                <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-indigo-600 text-white rounded-md">更新する</button>
            </div>
        </form>
    </div>
</div>
                        </form>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                    <div v-for="post in posts.data" :key="post.id" class="border-b last:border-0 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-grow">
                                <h4 class="text-lg font-bold">{{ post.title }}</h4>
                                <div class="text-sm text-gray-500">
                                    <span>{{ post.user.name }}</span>
                                    <span class="ml-2">{{ new Date(post.created_at).toLocaleString('ja-JP') }}</span>
                                </div>
                            </div>
                            <div v-if="authUser && post.user.id === authUser.id" class="flex gap-2 flex-shrink-0 ml-4">
                                <button @click="openEditModal(post)" class="text-sm text-blue-500 hover:underline">編集</button>
                                <button @click="deletePost(post.id)" class="text-sm text-red-500 hover:underline">削除</button>
                            </div>
                        </div>

                            <p class="mt-2 text-gray-800 whitespace-pre-wrap">{{ post.body }}</p>

                            <div class="mt-4 pl-8 border-l-2">
                                <div v-for="comment in post.comments" :key="comment.id" class="mb-3">
                                    <div class="text-sm text-gray-500">
                                        <span>{{ comment.user.name }}</span>
                                        <span class="ml-2">{{ new Date(comment.created_at).toLocaleString('ja-JP') }}</span>
                                    </div>
                                    <p class="mt-1 text-gray-800 whitespace-pre-wrap">{{ comment.body }}</p>
                                </div>

                                <div class="mt-4" @vue:mounted="initializeCommentForm(post.id)">
                                    <form @submit.prevent="submitComment(post.id)" v-if="commentForms[post.id]">
                                        <textarea
                                            v-model="commentForms[post.id].body"
                                            placeholder="コメントを追加..."
                                            rows="2"
                                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        ></textarea>
                                        <div v-if="commentForms[post.id].errors.body" class="text-sm text-red-600 mt-1">
                                            {{ commentForms[post.id].errors.body }}
                                        </div>
                                        <button
                                            type="submit"
                                            :disabled="commentForms[post.id].processing"
                                            class="mt-2 px-3 py-1 bg-gray-500 text-white text-xs font-semibold rounded-lg shadow-md hover:bg-gray-700"
                                        >
                                            コメントする
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                    <template v-for="(link, key) in posts.links" :key="key">
                        <Link
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-2 mx-1 text-sm rounded-md"
                            :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-700 hover:bg-gray-200': !link.active, 'text-gray-400': !link.url }"
                            :disabled="!link.url"
                        />
                    </template>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>