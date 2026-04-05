<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function create(Client $client){
        return view('visits.create', compact('client'));
    }

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



    //画面編集
    public function edit(Visit $visit){
        return view('visits.edit', compact('visit'));
    }

    //更新処理
    public function update(Request $request, $id)
    {
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


    //削除
    public function destroy($id)
    {
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

}
