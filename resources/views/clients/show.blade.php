@vite(['resources/css/app.css', 'resources/js/app.js'])

<h1>個人ページ</h1>

<div id="client-info">
    <div>名前：<span id="client-name">読み込み中...</span></div>
    <div>特徴メモ：<span id="client-memo">読み込み中...</span></div>
</div>

<hr>

<div id="add-visit-link"></div>

<h2>訪問履歴</h2>
<div id="visit-list">履歴を読み込み中...</div>

<br>

<a href="/clients">一覧に戻る</a>

<script>
    const clientId = window.location.pathname.split('/').pop();
    console.log("取得したID：", clientId);
</script>