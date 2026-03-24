<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<h1>訪問一覧(API接続)</h1>

<div style="margin-bottom: 20px;">
    <a href="/clients/create" style="background: #eee; padding: 5px 10px; border: 1px solid #ccc; text-decoration: none; color: black;">新規登録</a>
</div>

<hr>

<div id="client-list">読み込み中...</div>

@if (session('message'))
    <p style="color: red;">{{ session('message') }}</p>
@endif

