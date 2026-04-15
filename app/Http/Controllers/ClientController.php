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


    // JSからアクセスしたときにapi.phpから呼ばれる「データ」を返す
    public function apiIndex(Request $request)
    {
        $keyword = $request->query('keyword');

        $sort = $request->query('sort', 'last_visit_at');
        $order = $request->query('order', 'desc');

        // クエリビルダを開始
        $query = Client::query();

        // もしキーワードがあれば絞り込み
        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('last_name', 'like', "%{$keyword}%")
                ->orWhere('first_name', 'like', "%{$keyword}%")
                ->orWhere('last_name_kana', 'like', "%{$keyword}%")
                ->orWhere('first_name_kana', 'like', "%{$keyword}%");
            });
        }

        // 並び替えの実行
        $allowedSorts = ['last_name_kana', 'updated_at', 'created_at', 'is_favorite'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order);
        } else {
            // 想定外のときはデフォルトの並び順
            $query->orderBy('last_visit_at', 'desc');
        }



        // 最後にページネーションを実行
        $clients = $query->paginate(10);
        // $clients = $query->orderBy('last_name_kana', 'asc')->paginate(10);

        return response()->json($clients);
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
        // dd($request->all());
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
