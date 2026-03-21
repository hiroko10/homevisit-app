<h1>訪問一覧ページ</h1>

<a href="/clients/create">新規登録</a>

@foreach ($clients as $client) 
    <div>
        <a href="/clients/{{ $client->id }}">
        {{ $client->name }}
        </a>
    </div>
@endforeach