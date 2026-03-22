<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<h1>訪問一覧</h1>

<a href="/clients/create">新規登録</a><br><br>

@if (session('message'))
    <p style="color: red;">{{ session('message') }}</p>
@endif

@foreach ($clients as $client) 
    <div>
        <a href="/clients/{{ $client->id }}">
        {{ $client->name }}
        </a>
    </div>

    <form action="/clients/{{ $client->id }}" method="POST" onsubmit="return confirm('本当に削除しますか？')">
        @csrf
        @method('DELETE')
        <button type="submit">削除</button>
    </form>
@endforeach