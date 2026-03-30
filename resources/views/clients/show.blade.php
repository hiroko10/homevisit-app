@vite(['resources/css/app.css', 'resources/js/app.js'])

<h1>個人ページ</h1>

<div id="client-info">
    <div>名前：<span id="client-name">読み込み中...</span></div>
    <div>特徴メモ：<span id="client-memo">読み込み中...</span></div>
</div>

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