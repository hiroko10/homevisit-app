<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     * ブラウザで/clientsの画面へアクセス時にweb.phpから呼ばれる
     */
    public function index(){
        return view('clients.index');
    }


    // JSからアクセスしたときにapi.phpから呼ばれるデータを返す
    public function apiIndex(Request $request) {
        // 1. パラメータの取得
        $keyword = $request->query('keyword');
        $sort = $request->query('sort', 'updated_at'); //デフォルト値
        $order = $request->query('order', 'desc');

        // 2. クエリの開始・検索
        $query = Client::search($keyword); //Model呼び出してscope

        // 3. ソート処理
        if ($sort === 'last_name_kana') {
            $query->sortByKana($order); //Model
        } else {
            $query->orderBy($sort, $order); //更新日・お気に入り等、普通の並び替え
        }

        // 4. 結果を返却
        return response()->json($query->paginate(10));
    }




    // 特定のクライアント内の訪問履歴を検索するAPI
    public function apiSearchActivities(Request $request, $id) {
        $keyword = $request->query('keyword');

        // 1. そのクライアントが存在するか確認し、紐づく活動記録のクエリを開始
        $client = Client::findOrFail($id);
        $query = $client->activities(); // Clientモデルにactivitiesリレーションがある前提

        // 2. キーワードがあれば絞り込み
        if (!empty($keyword)) {
            $query->where('content', 'like', "%{$keyword}%");
        }

        // 3. 取得して返却
        $results = $query->orderBy('created_at', 'desc')->get();
        return response()->json($results);
    }

    // 非同期型の保存
    public function apiStore(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name_kana' => 'required|string|max:255',
            'first_name_kana' => 'required|string|max:255',
            'memo' => 'nullable|string',
        ]);

        // 保存
        $client = Client::create($validated);

        // JSONで結果を返す
        return response()->json($client, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show() {
        return view('clients.show');
    }

    public function apiShow($id) {
        $client = Client::findOrFail($id);

        return response()->json($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'last_name_kana' => 'nullable',
            'first_name_kana' => 'nullable',
            'memo' => 'nullable',
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function apiDestroy($id) {
        $client = Client::findOrFail($id); //IDを元にclientを削除
        $client->delete();

        return response()->json(['message', '削除しました']); //削除成功したことをJSONで返す
    }



    public function toggleFavorite(Request $request, Client $client)
    {
        // JavaScriptから送られてきた boolean (true/false) を受け取る
        $isFavorite = $request->input('is_favorite');

        // DBを更新
        $client->update([
            'is_favorite' => $isFavorite
        ]);

        // 結果をJSON形式で返す
        return response()->json([
            'success' => true,
            'is_favorite' => $client->is_favorite
        ]);
    }


}
