<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // axios(JS)から呼ばれるデータ専用窓口
    public function apiIndex(){
        $clients = Client::paginate(10);
        return response()->json($clients);
    }


    /**
     * Display a listing of the resource. ブラウザで/clientsへアクセス時に呼ばれる
     */
    public function index()
    {
        return view('clients.index');
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

    // ClientController.php

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
}
