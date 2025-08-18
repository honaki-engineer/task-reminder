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
                            {{-- Slack連携 --}}
                            <div class="flex flex-col items-center justify-center min-h-[400px] space-y-6">
                                {{-- <a href="{{ route('slack.authorize') }}" class="inline-block w-60 text-center py-3 bg-gray-200 rounded border text-lg hover:bg-gray-300">
                                    ① Slack連携/解除
                                </a> --}}
                                @if($slackNotification && $slackNotification->isLinked())
                                    <form action="{{ route('slack.disconnect') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="inline-block w-60 text-center py-3 text-white bg-pink-500 rounded border text-lg hover:bg-pink-600" onclick="return confirm('連携解除しますか？');">
                                            Slack連携解除
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('slack.authorize') }}" class="inline-block w-60 text-center py-3 text-white bg-green-500 rounded border text-lg hover:bg-green-600">
                                        Slack連携
                                    </a>
                                @endif
                                <a href="#" class="inline-block w-60 text-center py-3 bg-gray-200 rounded border text-lg hover:bg-gray-300">
                                    ② 通知On/Off
                                </a>
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
