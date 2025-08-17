<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', '消費期限管理') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col font-sans antialiased items-center justify-center bg-gray-100">
    <main class="flex-grow flex flex-col items-center justify-center text-center">
        <div class="text-center">
            <!-- タイトル -->
            <h1 class="text-4xl font-bold text-gray-800 mb-6">{{ config('app.name') }}</h1>
    
            @if (Route::has('login'))
                <!-- ボタンコンテナ -->
                <div class="space-x-4">
                    @auth
                        <a href="{{ route('tasks.one_day') }}" class="px-6 py-3 bg-gray-800 text-white text-lg font-semibold rounded-lg shadow-md 
                                    hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
                                    transition duration-300">トップページへ</a>
                    @else
                        <!-- ログインボタン -->
                        
                        <a href="{{ route('login') }}"
                            class="px-6 py-3 bg-gray-800 text-white text-lg font-semibold rounded-lg shadow-md 
                                    hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 
                                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 
                                    transition duration-300">
                            ログイン
                        </a>
    
                        @if (Route::has('register'))
                            <!-- 新規登録ボタン -->
                            <a href="{{ route('register') }}"
                                class="px-6 py-3 bg-gray-400 text-white text-lg font-semibold rounded-lg shadow-md 
                                        hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-500 
                                        focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 
                                        transition duration-300">
                                新規登録
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </main>

    {{-- フッター --}}
    <x-footer-top />
</body>
</html>
