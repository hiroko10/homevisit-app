<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KATEIHOUMON') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ページ全体の構造：footerを常に下に */
            .main-container {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                background-color: #E8F6EE; /* 薄いグレーの背景 */
            }

            /* 透明ヘッダー（スクロールしても固定） */
            .custom-header {
                position: sticky;
                top: 0;
                z-index: 50;
                width: 100%;
                background-color: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(8px); /* 背景ぼかし */
                border-bottom: 1px solid #e5e7eb;
            }

            .header-inner {
                max-width: 1200px;
                margin: 0 auto;
                padding: 1rem 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .logo {
                font-weight: bold;
                font-size: 1.25rem;
                color: #111827;
                text-decoration: none;
                cursor: pointer;
            }

            .logo:hover {
                opacity: 0.7;
            }

            .nav-links {
                display: flex;
                gap: 1.5rem;
                align-items: center;
            }

            .nav-item {
                text-decoration: none;
                color: #374151;
                font-size: 0.95rem;
            }

            .logout-button {
                border: 1px solid #374151;
                background:  rgba(255, 255, 255, 0.1);
                padding: 0.4rem 1.2rem;
                border-radius: 0.5rem;
                cursor: pointer;
                font-size: 0.9rem;
            }

            /* Main contents */
            main {
                flex-grow: 1; /* これでフッターを下に押し出す */
                width: 100%;
                max-width: 1200px;
                margin: 0 auto;
                padding: 2rem 1.5rem;
            }

            /* Footer */
            .custom-footer {
                width: 100%;
                padding: 1rem 0;
                background-color: #CFEDDD;
                text-align: center;
                color: #6b7280;
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="main-container">
            <header class="custom-header">
                <div class="header-inner">
                    <a href="/clients" class="logo">KATEIHOUMON</a>
                    <nav class="nav-links">
                        {{-- <a href="/create" class="nav-item">新規入力</a> --}}
                        <a href="/clients" class="nav-item">訪問一覧</a>
                        
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                            @csrf
                        </form>

                        <button type="button" class="logout-button" 
                            onclick="event.preventDefault(); 
                            document.getElementById('logout-form').submit();">
                            Log Out
                        </button>
                    </nav>
                </div>
            </header>

            {{-- page content --}}
            <main>
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="custom-footer">
                KATEIHOUMON
            </footer>
        </div>
    </body>
</html>
