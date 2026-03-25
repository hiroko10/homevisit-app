<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function apiIndex(){
        $clients = Client::all();
        return response()->json($clients);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::all();
        // dd($clients);
        return view('clients.index', compact('clients'));
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

    /**
     * Display the specified resource.
     */
    public function show(){
        return view('clients.show');
    }


    public function apiShow($id)
    {
        $client = Client::with('visits')->findOrFail($id);

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
    public function update(Request $request, string $id)
    {
        //
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
