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
    public function store(Request $request, Client $client){
        Visit::create([
            'client_id' => $client->id,
            'visited_at' => $request->visited_at,
            'content' => $request->content,
            // 'memo' => $request->memo,
        ]);

        return redirect()->route('clients.show', $client);
    }

    //画面編集
    public function edit(Visit $visit){
        return view('visits.edit', compact('visit'));
    }
    //更新処理
    public function update(Request $request, Visit $visit){
        $visit->update([
            'visited_at' => $request->visited_at,
            'content' => $request->content,
            'memo' => $request->memo,
        ]);

        return redirect()->route('clients.show', $visit->client);
    }

    //削除
    public function destroy(Visit $visit){
        $client = $visit->client; //リダイレクト用
        $visit->delete();
        return redirect()->route('clients.show', $client);
    }
}
