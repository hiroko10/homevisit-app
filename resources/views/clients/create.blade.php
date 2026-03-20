<h1>訪問先 新規登録</h1>

<form method="POST" action="/clients">
    @csrf

    <div>
        名前： <input type="text" name="name">
    </div>

    <div>
        メモ： <textarea name="memo" id="" cols="30" rows="10"></textarea>
    </div>

    <button type="submit">登録</button>
</form>