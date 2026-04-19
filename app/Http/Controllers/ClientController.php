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
    public function apiIndex(Request $request)
    {
        // 1. パラメータの取得
        $keyword = $request->query('keyword');
        $sort = $request->query('sort');
        $order = $request->query('order');

        // 2. クエリの開始
        $query = Client::query();

        // 3. 検索処理
        // キーワードがある場合のみwhereを追加
        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('last_name', 'like', "%{$keyword}%")
                ->orWhere('first_name', 'like', "%{$keyword}%")
                ->orWhere('last_name_kana', 'like', "%{$keyword}%")
                ->orWhere('first_name_kana', 'like', "%{$keyword}%");
            });
        }

        // 4. ソート処理（苗字のフリガナを純粋な文字として再定義した上で、指定された方向（昇順か降順か）で並び替え）
        if ($sort === 'last_name_kana') {
        // 名前
            $query->orderByRaw("CAST(last_name_kana AS CHAR) {$order}")
                ->orderByRaw("CAST(first_name_kana AS CHAR) {$order}");
            
        } else if ($sort === 'updated_at') {
            // 更新日
            $query->orderBy('updated_at', $order);
            
        } else if ($sort === 'is_favorite') {
            // お気に入り
            $query->orderBy('is_favorite', $order);
            
        } else {
            // それ以外（初期表示など）は最新順
            $query->orderBy('updated_at', 'desc');
    }


        // 5. 結果を返却
        return response()->json($query->paginate(10));
    }

    


    // 特定のクライアント内の訪問履歴を検索するAPI
    public function apiSearchActivities(Request $request, $id)
    {
    $keyword = $request->query('keyword');

    // まずそのクライアントが存在するか確認し、紐づく活動記録のクエリを開始
    $client = Client::findOrFail($id);
    $query = $client->activities(); // Clientモデルにactivitiesリレーションがある前提

    if (!empty($keyword)) {
        $query->where('content', 'like', "%{$keyword}%");
    }

    $results = $query->orderBy('created_at', 'desc')->get();

    return response()->json($results);
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        Client::create([
            'name' => $request->name,
            'memo' => $request->memo,
        ]);

        return redirect('/clients');
    }


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
    public function show(){
        return view('clients.show');
    }


    public function apiShow($id)
    {
        $client = Client::findOrFail($id);

        return response()->json($client);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) 
    {
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
    public function apiDestroy($id)
    {
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
