<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            タスク一覧
        </h2>
    </x-slot>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- セクション --}}
                    <section class="text-gray-600 body-font relative">
                        <div class="container px-5 mx-auto">
                            {{-- フラッシュメッセージ --}}
                            @if(session('success'))
                                <div class="overflow-x-auto mx-auto overflow-auto">
                                    <div id="flash-message"
                                        class="inline-block bg-green-100 text-green-800 rounded px-4 py-2 mb-4 transition-opacity duration-1000">
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif
                            {{-- 全体のコンテンツ --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($taskCategories as $taskCategory)
                                    {{-- css --}}
                                    @php
                                        $slug = $taskCategory->slug ?? 'default';
                                        $list = $tasksByCategory[$taskCategory->id] ?? collect();
                                    @endphp
                                    {{-- 1/4のコンテンツ --}}
                                    <div class="rounded-xl border border-gray-200 overflow-hidden shadow-xl flex flex-col h-80">
                                        {{-- ヘッダー（固定） --}}
                                        <div class="flex items-center justify-between px-3 py-2 bar--{{ $slug }} shrink-0">
                                            <div class="font-semibold">{{ $taskCategory->name }}</div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs bg-white/30 rounded-full px-2 py-0.5">{{ $list->count() }}</span>
                                                <a href="{{ route('tasks.create') }}"
                                                    class="w-6 h-6 rounded-full bg-white/40 grid place-items-center hover:bg-white/70">＋</a>
                                            </div>
                                        </div>
                                        {{-- 本体リスト --}}
                                        <ul class="panel--{{ $slug }} p-3 flex-1 overflow-y-auto{ -webkit-overflow-scrolling: touch; } overflow-y-scroll">
                                            @forelse($list as $task)
                                                <li class="py-1">
                                                    <div class="flex items-start gap-2">
                                                        <input type="checkbox" class="rounded shrink-0">
                                                        <span class="text-sm text-gray-800 leading-tight break-words">
                                                            {{ $task->title }}
                                                            @if(!empty($task->description))
                                                                <span class="ml-1 align-text-top text-xs text-gray-500">💬</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="pl-6 text-xs text-gray-500">{{ $task->end_at->format('Y/m/d H:i') }}</div>
                                                </li>
                                            @empty
                                                <li class="text-sm text-gray-400 italic">項目はありません</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // フラッシュメッセージを10秒後にフェードアウトし、さらに2秒後に削除する
    setTimeout(() => {
        const flashMessage = document.getElementById('flash-message');
        if(flashMessage) {
            flashMessage.classList.add('opacity-0'); // フェードアウト
            setTimeout(() => flashMessage.remove(), 2000); // 2秒後に flashMessage というHTML要素を DOM(画面上)から完全に削除
        }
    }, 10000); // 10秒後にフェード開始
});
</script>
</x-app-layout>
