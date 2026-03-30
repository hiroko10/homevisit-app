<head>
    <script src="https://unpkg.com/vanilla-autokana@1.1.0/dist/autokana.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<h1>訪問一覧</h1>

<div id="add-client-wrapper" style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #d1d9e0;">
    <h3 style="margin-top: 0;">新規登録</h3>

    <div style="display: flex; flex-direction: column; gap: 15px;">

        <div style="display:flex; gap: 10px;">
            <p>氏　名</p>
            <input type="text" id="new-client-last-name" placeholder="姓" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" id="new-client-first-name" placeholder="名" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="display:flex; gap: 10px;">
            <p>ふりがな</p>
            <input type="text" id="new-client-last-name-kana" placeholder="せい" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <input type="text" id="new-client-first-name-kana" placeholder="めい" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div>
            <p>特徴メモ</p>
            <input type="text" id="new-client-memo" placeholder="特徴メモ（その人の特徴をメモしてください）" style="width: 100%; padding: 8px; border-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1;">
        </div>

        <div style="text-align: right;">
            <button type="button" onclick="addClient()" style="background: #007bff; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                登録する
            </button>
        </div>

    </div>
</div>
<hr>

<div id="client-list">読み込み中...</div>

@if (session('message'))
    <p style="color: red;">{{ session('message') }}</p>
@endif

