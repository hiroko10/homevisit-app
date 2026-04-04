@vite(['resources/css/app.css', 'resources/js/app.js'])

<h1>個人ページ</h1>

<div id="client-info" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
    <div id="client-view-mode">
        <div style="font-size: 0.8rem; color: #888;" id="client-kana"></div>
        <div style="font-size: 1.5rem; font-weight: bold; margin-bottom: 10px;">
            名前：<span id="client-name"></span>
        </div>
        <div>特徴メモ：<span id="client-memo"></span></div>
        <button onclick="enableClientEdit()" style="margin-top:10px; font-size:0.8rem;">基本情報を編集</button>
    </div>

    <div id="client-edit-mode" style="display: none;">
        <input type="text" id="edit-client-last-name-kana" placeholder="せい" style="font-size:0.7rem;">
        <input type="text" id="edit-client-first-name-kana" placeholder="めい" style="font-size:0.7rem;"><br>
        
        <input type="text" id="edit-client-last-name" placeholder="姓" style="font-size:1.2rem; font-weight:bold;">
        <input type="text" id="edit-client-first-name" placeholder="名" style="font-size:1.2rem; font-weight:bold;"><br>
        
        <textarea id="edit-client-memo" style="width:100%; margin-top:10px;"></textarea>
        
        <div style="margin-top:10px;">
            <button onclick="updateClientInfo()" style="background:#28a745; color:white;">保存</button>
            <button onclick="cancelClientEdit()" style="background:#6c757d; color:white;">キャンセル</button>
        </div>
    </div>
</div>


{{-- 

<div id="client-info" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
    <div style="font-size: 0.8rem; color: #888;" id="client-kana">読み込み中...</div>
    <div style="font-size: 1.5rem; font-weight: bold; margin-bottom: 10px;">
    <div>名前：<span id="client-name">読み込み中...</span></div>
    <div>特徴メモ：<span id="client-memo">読み込み中...</span></div>
</div> --}}

<hr>

{{-- 個人ページ内での訪問履歴の追加 --}}
<div id="add-visit-container" style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
    <h3>訪問履歴の追加</h3>
    <div style="margin-bottom: 10px;">
        <label>訪問日：</label><br>
        <input type="datetime-local" id="new-visit-at" style="border-radius: 8px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label>内容：</label><br>
        <textarea id="new-visit-content" rows="3" style="width: 100%; border-radius: 8px;" placeholder="訪問した内容をご入力"></textarea>
    </div>
    <button type="button" onclick="addVisit()" style="background: #57b8ce; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
        訪問履歴の保存
    </button>
</div>


{{-- 訪問履歴 --}}
<h2>訪問履歴</h2>
<div id="visit-list">履歴を読み込み中...</div>

<br>

<a href="/clients">一覧に戻る</a>

<script>
    const clientId = window.location.pathname.split('/').pop();
    console.log("取得したID：", clientId);
</script>