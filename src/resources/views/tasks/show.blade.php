<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            タスク詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div @class(['overflow-hidden shadow-sm sm:rounded-lg',
                        'bg-white' => ! $task->is_completed,
                        'bg-green-50' => $task->is_completed])>
                <div class="p-6 text-gray-900">
                {{-- 戻るボタンの遷移先を取得 --}}
                @if(Str::contains($backUrl, '/one-day'))
                    <a href="{{ $backUrl }}"
                        class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 transition-all duration-150 shadow-sm hover:shadow">
                        ← OneDayタスクへ戻る
                    </a>
                @elseif(Str::contains($backUrl, '/tasks'))
                    <a href="{{ $backUrl }}"
                        class="px-4 py-2 rounded bg-gray-100 hover:bg-gray-200 transition-all duration-150 shadow-sm hover:shadow">
                        ← タスク一覧へ戻る
                    </a>
                @endif

                    <section class="text-gray-600 body-font relative mt-4">
                        <div class="container px-5 mx-auto">
                            <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                {{-- フラッシュメッセージ --}}
                                @if(session('success'))
                                    <div class="overflow-x-auto max-w-[794px] mx-auto overflow-auto">
                                        <div id="flash-message"
                                            class="inline-block bg-green-100 text-green-800 rounded px-4 py-2 mb-4 transition-opacity duration-1000">
                                            {{ session('success') }}
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-wrap mt-2">
                                    {{-- 完了タグ --}}
                                    @if($task->is_completed)
                                        <div class="p-2 w-full">
                                            <span class="bg-green-500 text-white px-2 py-1 leading-none rounded-full text-sm">
                                                完了済みタスクです
                                            </span>
                                        </div>
                                    @endif
                                    {{-- タイトル --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="title" class="leading-7 text-sm text-gray-600">タスク</label>
                                            <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $task->title }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- 詳細 --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="description" class="leading-7 text-sm text-gray-600">詳細</label>
                                            <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200  min-h-32 max-h-96 text-base outline-none text-gray-700 py-1 px-3 resize-y leading-6 transition-colors duration-200 ease-in-out overflow-y-scroll">
                                                {{-- {{ $task->description }} --}}
                                                {!! nl2br(e($task->description)) !!}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- フォーカスマトリックス --}}
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="task_category" class="leading-7 text-sm text-gray-600">フォーカスマトリックス</label>
                                            <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $task->taskCategory->name }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- 開始日時 --}}
                                    <div class="p-2 w-full">
                                    <fieldset class="relative flex gap-2">
                                        <legend class="leading-7 text-sm text-gray-600 block">開始日時</legend>
                                        <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $task->start_at->format('Y/m/d') }}
                                        </div>
                                        <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $task->start_at->format('H:i') }}
                                        </div>
                                    </fieldset>
                                    </div>
                                    {{-- 締切日時 --}}
                                    <div class="p-2 w-full">
                                    <fieldset class="relative flex gap-2">
                                        <legend class="leading-7 text-sm text-gray-600 block">締切日</legend>
                                        <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $task->end_at->format('Y/m/d') }}
                                        </div>
                                        <div class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            {{ $task->end_at->format('H:i') }}
                                        </div>
                                    </fieldset>
                                    </div>

                                    {{-- ボタンエリア --}}
                                    <div class="w-full p-2 flex flex-col sm:flex-row gap-4 justify-center">
                                        {{-- 完了 --}}
                                        <form action="{{ route('tasks.complete', ['task' => $task->id ]) }}" method="POST" id="completeTaskForm">
                                            @csrf
                                            <input type="hidden" name="back_url" value="{{ $backUrl ?? request('back_url') }}">
                                            <button type="submit" name="action"
                                                class="w-full text-white bg-green-500 border-0 py-2 px-8 focus:outline-none hover:bg-green-600 rounded text-lg">
                                                {{ $task->is_completed ? '未完了に戻す' : '完了' }}
                                            </button>
                                        </form>
                                        {{-- 編集 --}}
                                        <a href="{{ route('tasks.edit', ['task' => $task->id, 'back_url' => $backUrl ]) }}"
                                            class=" text-white text-center bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                                            編集
                                        </a>
                                        {{-- 削除 --}}
                                        <form id="delete_{{ $task->id }}" action="{{ route('tasks.destroy', ['task' => $task->id ]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="back_url" value="{{ $backUrl ?? request('back_url') }}">
                                            <a href="#" data-id="{{ $task->id }}" onclick="deleteTask(this)"
                                                class="w-full inline-block text-center text-white bg-pink-500 border-0 py-2 px-8 focus:outline-none hover:bg-pink-600 rounded text-lg">
                                                削除
                                            </a>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ----- フラッシュメッセージを10秒後にフェードアウトし、さらに2秒後に削除する
    setTimeout(() => {
        const flashMessage = document.getElementById('flash-message');
        if(flashMessage) {
            flashMessage.classList.add('opacity-0'); // フェードアウト
            setTimeout(() => flashMessage.remove(), 2000); // 2秒後に flashMessage というHTML要素を DOM(画面上)から完全に削除
        }
    }, 10000); // 10秒後にフェード開始


    // ----- 確認メッセージ
    function deleteTask(e) {
        'use strict'
        if(confirm('本当に削除していいですか？')) {
            document.getElementById('delete_' + e.dataset.id).submit()
        }
    }
    window.deleteTask = deleteTask; // 関数をグローバルに登録


    // 二重送信防止
    document.getElementById('completeTaskForm').addEventListener('submit', function (e) {
        const btn = e.submitter; // 押された送信ボタン
        setTimeout(() => btn.disabled = true, 0); // 送信後すぐ無効化
    });
});
</script>
</x-app-layout>
