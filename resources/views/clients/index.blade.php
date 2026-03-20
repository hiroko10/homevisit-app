<h1>個人ページ</h1>

<a href="/clients/create">新規登録</a>

@foreach ($clients as $client) 
    <div>
        {{ $client->name }}
    </div>
@endforeach