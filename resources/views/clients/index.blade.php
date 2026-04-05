<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<h1>訪問一覧</h1>

<button id="show-add-client-btn" onclick="enableAddClientForm()" style="margin-bottom: 20px; background: #57b8ce; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
    ＋ 新規登録
</button>

<div id="add-client-container" style="display: none; background: #fbfeff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
    <h3>新規登録</h3>
    
    
    <div style="margin-bottom: 10px;">
        <label>氏名：</label><br>
        <input type="text" id="new-client-last-name" placeholder="姓" style="border-radius: 4px">
        <input type="text" id="new-client-first-name" placeholder="名" style="border-radius: 4px">
    </div>
    <div style="margin-bottom: 10px;">
        <label>かな：</label><br>
        <input type="text" id="new-client-last-name-kana" placeholder="せい" style="border-radius: 4px">
        <input type="text" id="new-client-first-name-kana" placeholder="めい" style="border-radius: 4px">
    </div>
    <div style="margin-bottom: 15px;">
        <label>特徴メモ：</label><br>
        <textarea id="new-client-memo" placeholder="特徴メモ" style="width: 100%; border-radius:4px"></textarea>
    </div>

    <div style="text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
        <button type="button" onclick="addClient()" style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">登録する</button>
        <button type="button" onclick="cancelAddClientForm()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">キャンセル</button>
    </div>
</div>

<hr>

<div id="client-list">読み込み中...</div>




@if (session('message'))
    <p style="color: red;">{{ session('message') }}</p>
@endif

