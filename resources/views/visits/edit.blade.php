<h1>訪問履歴編集</h1>

<form method="POST" action="/visits/{{ $visit->id }}">
    @csrf
    @method('PUT')

    <label for="">訪問履歴</label><br>
    <input type="datetime-local" name="visited_at" value="{{ $visit->visited_at }}"><br><br>

    <label for="">内容</label>
    <textarea name="content" id="" cols="30" rows="10">{{ $visit->content }}</textarea><br><br>

    {{-- <label for="">メモ</label>
    <textarea name="memo" id="" cols="30" rows="10">{{ $visit->memo }}</textarea><br><br> --}}

    <button type="submit">更新</button>
</form>