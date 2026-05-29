<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KATEIHOUMON') }}</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Abel&family=Bitcount+Grid+Single:wght@100..900&family=Noto+Sans+JP:wght@100..900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased">
        {{-- main-container --}}
        <div class="min-h-screen flex flex-col bg-[#E8F6EE]">
            {{-- header --}}
            <header class="sticky top-0 z-50 w-full bg-white/10 backdrop-blur-md border-b border-gray-200">
                {{-- header-inner--}}
                <div class="max-w-[1200px] mx-auto px-6 py-4 flex justify-between items-center">
                    {{-- logo --}}
                    <a href="/clients" class="text-[1.25rem] font-bold text-gray-900 no-underline cursor-pointer hover:text-[#0FA69D]">KATEIHOUMON</a>
                    {{-- navigation--}}
                    <nav class="flex items-center gap-6">
                        {{-- nav-item --}}
                        <a href="/clients" class="text-[0.95rem] text-gray-700 no-underline hover:text-[#0FA69D]">訪問先一覧</a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>

                        {{-- logout-button --}}
                        <button type="button" 
                                class="text-[0.9rem] border border-gray-600 text-gray-700 bg-white/10 rounded-[8px] px-[1.2rem] py-[0.4rem] cursor-pointer transition-colors duration-200 hover:bg-[#0FA69D] hover:border-[#0FA69D] hover:text-white" 
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Log Out
                        </button>
                    </nav>
                </div>
            </header>

            {{-- main --}}
            <main class="flex-grow w-full max-w-[1200px] mx-auto px-6 py-8">
                {{ $slot }}
            </main>

            {{-- footer --}}
            <footer class="w-full py-4 bg-[#CFEDDD] text-center text-gray-500 text-[0.875rem]">
                KATEIHOUMON
            </footer>
        </div>
    </body>
</html>
