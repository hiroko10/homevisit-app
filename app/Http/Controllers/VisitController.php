<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    // ==＝＝index:一覧表示(READ)＝==＝
    public function index(Request $request) {
        // 1. バリデーション(入力)チェック
        $request->validate([
        // client_idは必須(required)で、かつclientsテーブルのidに実在すること(exists)
        'client_id' => 'required|exists:clients,id',

        // sortは任意だが、送るなら 'visited_at' か 'is_favorite' のどちらかであること
        'sort' => 'nullable|in:visited_at,is_favorite',

        // orderも任意だが、送るなら 'asc' か 'desc' のどちらかであること
        'order' => 'nullable|in:asc,desc',
        ]);


        // 2. バリテーションチェックを通過したデータのみ使用し処理開始
        // JSの axios.get(`/api/visits?client_id=${id}...`) から IDとkeywordを受け取る
        $clientId = $request->query('client_id');
        $keyword = $request->query('keyword');
        $sort = $request->query('sort', 'visited_at'); // 第2引数はデフォルト値
        $order = $request->query('order', 'desc');

        // Modelに該当IDのデータを取ってきてと依頼
        $visits = Visit::query()->where('client_id', $clientId)
               ->search($keyword)  // Visit.phpのモデルで定義したスコープを呼び出す
               ->orderBy($sort, $order)
               ->paginate(10);

        // JSONでフロントエンドに送り返す
        return response()->json($visits);
    }

    // ====create:登録画面(表：入力画面を作る/HTMLを返す) (CREATE:準備)====
    public function create(Client $client){
        return view('visits.create', compact('client'));
    }

    // ====store:保存実行(裏：DBに書き込む) (CREATE:実行)====
    public function store(Request $request){
        // 1. バリデーション
        // $request->client_id は、app.jsの axios.post で送っている名前と一致
        $validated = $request->validate([
            'client_id'  => 'required|exists:clients,id',
            'visited_at' => 'required',
            'content'    => 'required',
        ]);

        // 2. 保存
        // URLからではなく、$requestの中からIDを取り出して保存
        $visit = Visit::create([
            'client_id'  => $request->client_id,
            'visited_at' => $request->visited_at,
            'content'    => $request->content,
        ]);

        // 3. APIへの返事（JSON形式）
        return response()->json($visit, 201);
    }



    // ====edit:画面編集(表：入力画面を作る/HTMLを返す) (UPDATE:準備)====
    public function edit(Visit $visit){
        return view('visits.edit', compact('visit'));
    }

    // ====update:更新処理(裏：DBに書き込む) (UPDATE:実行)====
    public function update(Request $request, $id) {
        // 1. 該当する履歴を探す
        $visit = Visit::findOrFail($id);

        // 2. バリデーション（入力チェック）
        $validated = $request->validate([
            'visited_at' => 'required',
            'content'    => 'required|string',
        ]);

        // 3. データを更新して保存
        $visit->update($validated);

        // 4. JSONで「成功」と返す
        return response()->json($visit);
    }


    // ====destroy:削除 (DELETE)====
    public function destroy($id) {
        try {
            // IDで検索（見つからなければ404を出す）
            $visit = Visit::findOrFail($id);
            $visit->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // エラーの内容をJSONで返して、ブラウザで確認できるようにする
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // 訪問履歴のお気に入りの状態切り替え ====Update====
    public function toggleFavorite(Request $request, $id) {
        // 1. 該当する履歴を探す
        $visit = Visit::findOrFail($id);

        // 2. バリデーション（入力チェック）
        $validated = $request->validate([
            'is_favorite' => 'required|boolean',
        ]);

        // 3. データを更新
        $visit->update([
            'is_favorite' => $validated['is_favorite'],
        ]);

        // 4. JSONで「成功」と返す
        return response()->json(['success' => true, 'visit' => $visit]);
    }

}
