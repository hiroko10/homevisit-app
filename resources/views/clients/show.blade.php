<h1>個人ページ</h1>

<div>
    名前：{{ $client->name }}
</div>

<div>
    メモ：{{ $client->memo }}
</div>

<a href="/clients/{{ $client->id }}/visits/create">+訪問履歴を追加</a>


<h2>訪問履歴</h2>

@foreach ($client->visits as $visit)
    <div>
        日時：{{ $visit->visited_at }}<br>
        内容：{{ $visit->content }}
    </div>

    <a href="/visits/{{ $visit->id }}/edit">編集</a>

    <form method="POST" action="/visits/{{ $visit->id }}">
        @csrf
        @method('DELETE')
        <button type="submit">削除</button>
    </form>

@endforeach



<a href="/clients">一覧に戻る</a>