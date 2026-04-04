<h1>{{ $client->name }} さんの訪問履歴登録</h1>

<form method="POST" action="/clients/{{ $client->id }}/visits">
    @csrf

    <label>訪問日時</label><br>
    <input type="datetime-local" name="visited_at"><br><br>

    <label>内容</label><br>
    <textarea name="content" id="" cols="30" rows="10"></textarea><br><br>

    <button type="submit">登録</button>
</form>