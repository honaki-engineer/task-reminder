<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            タスク新規作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <section class="text-gray-600 body-font relative">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <div class="container px-5 mx-auto">
                            <div class="lg:w-1/2 md:w-2/3 mx-auto">
                            <div class="flex flex-wrap -m-2">
                                {{-- 必須のcss設定 --}}
                                @php
                                    $badgeReq = 'ml-2 items-center rounded px-1 py-0.5
                                                text-[11px] font-semibold bg-red-500 text-white align-top';
                                @endphp

                                {{-- タイトル --}}
                                <div class="p-2 w-full">
                                <div class="relative">
                                    <label for="title" class="leading-7 text-sm text-gray-600">タスク<span class="{{ $badgeReq }}">必須</span></label>
                                    <input type="text" id="title" name="title" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                </div>
                                </div>
                                {{-- 詳細 --}}
                                <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="description" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="description" name="description" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-y leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                    </div>
                                </div>
                                {{-- フォーカスマトリックス --}}
                                <div class="p-2 w-full">
                                <div class="relative">
                                    <label for="task_category" class="leading-7 text-sm text-gray-600">フォーカスマトリックス<span class="{{ $badgeReq }}">必須</span></label>
                                    <select name="task_category" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        <option value="">選択してください</option>
                                        @foreach($taskCategories as $taskCategory)
                                            <option value="{{ $taskCategory->id }}">{{ $taskCategory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                </div>
                                {{-- 開始日時 --}}
                                <div class="p-2 w-full">
                                <fieldset class="relative flex gap-2">
                                    <legend class="leading-7 text-sm text-gray-600 block">開始日時<span class="{{ $badgeReq }}">必須</span></legend>
                                    <input type="date" id="start_date" name="start_date" class="picker-input w-1/2 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '00:00') }}" class="picker-input w-1/2 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                </fieldset>
                                </div>
                                {{-- 締切日時 --}}
                                <div class="p-2 w-full">
                                <fieldset class="relative flex gap-2">
                                    <legend class="leading-7 text-sm text-gray-600 block">締切日<span class="{{ $badgeReq }}">必須</span></legend>
                                    <input type="date" id="end_date" name="end_date" class="picker-input w-1/2 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', '23:59') }}" class="picker-input w-1/2 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                </fieldset>
                                </div>

                                {{-- ボタンエリア --}}
                                <div class="w-full p-2 flex flex-col sm:flex-row gap-4 justify-center">
                                    <button type="submit" name="action" value="store_and_index"
                                        class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                                        登録して終了
                                    </button>
                                    <button type="submit" name="action" value="store_and_create"
                                        class="text-white bg-green-500 border-0 py-2 px-8 focus:outline-none hover:bg-green-600 rounded text-lg">
                                        登録して続けて入力
                                    </button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </form>
                    </section>

                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.picker-input').forEach(input => {
        if (typeof input.showPicker === 'function') {
            input.addEventListener('click', () => input.showPicker());
        }
    });
});
</script>
</x-app-layout>
