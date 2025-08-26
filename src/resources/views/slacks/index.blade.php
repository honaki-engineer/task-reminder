<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Slack連携
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
                            {{-- Slack連携全体 --}}
                            <div class="flex flex-col items-center justify-center space-y-6">
                                {{-- Slack連携 & 解除 --}}
                                @if($slackNotification && $slackNotification->isLinked())
                                    <form class="slackForm" action="{{ route('slack.disconnect') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="inline-block w-60 text-center py-3 text-white bg-pink-500 rounded border text-lg hover:bg-pink-600" onclick="return confirm('連携解除しますか？');">
                                            Slack連携解除
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('slack.redirect') }}" class="inline-block w-60 text-center py-3 text-white bg-green-500 rounded border text-lg hover:bg-green-600">
                                        Slack連携
                                    </a>
                                @endif
                                {{-- 通知ON & OFFトグル：Slack連携済みのときだけ --}}
                                @if($slackNotification && $slackNotification->isLinked())
                                    @if($slackNotification->is_enabled)
                                        {{-- ON → OFFにするリンク表示 --}}
                                        <form class="slackForm" action="{{ route('slack.toggle') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-block w-60 py-3 text-white bg-pink-500 rounded hover:bg-pink-600">
                                                毎朝の通知をOFFにする
                                            </button>
                                        </form>
                                    @else
                                        {{-- OFF → ONにするリンク表示 --}}
                                        <form class="slackForm" action="{{ route('slack.toggle') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-block w-60 py-3 text-white bg-green-500 rounded hover:bg-green-600">
                                                毎朝の通知を通知ONにする
                                            </button>
                                        </form>
                                    @endif
                                @endif
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


    // 二重送信防止
    document.querySelectorAll('form.slackForm').forEach(form => {
        form.addEventListener('submit', function (e) {
            const btn = e.submitter;           // 押された送信ボタン
            setTimeout(() => btn.disabled = true, 0); // 送信直後に無効化
        });
    });
});
</script>
</x-app-layout>
