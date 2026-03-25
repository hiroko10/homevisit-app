<h1>個人ページ</h1>

<div id="client-info">
    <div>名前：<span id="client-name">読み込み中...</span></div>
    <div>特徴メモ：<span id="client-memo">読み込み中...</span></div>
</div>

<hr>

<div id="add-visit-link"></div>

{{-- <a href="/clients/{{ $client->id }}/visits/create">+訪問履歴を追加</a> --}}


<h2>訪問履歴</h2>
<div id="visit-list">履歴を読み込み中...</div>


{{-- @foreach ($client->visits as $visit)
    <div>
        日時：{{ $visit->visited_at }}<br>
        内容：{{ $visit->content }}
    </div>

    <a href="/visits/{{ $visit->id }}/edit">編集</a>

    <form method="POST" action="/visits/{{ $visit->id }}" method="POST" onsubmit="return confirm('本当に削除しますか？')">
        @csrf
        @method('DELETE')
        <button type="submit">削除</button>
    </form>

@endforeach --}}

<br>

<a href="/clients">一覧に戻る</a>

<script>
    const clientId = window.location.pathname.split('/').pop();
    console.log("取得したID：", clientId);
</script>