<h1>新規訪問先 個人名登録</h1>

<form method="POST" action="/clients">
    @csrf

    <div>
        名前： <input type="text" name="name">
    </div>

    <div>
        特徴メモ： <textarea name="memo" id="" cols="30" rows="1"></textarea>
    </div>

    <button type="submit">登録</button>
</form>