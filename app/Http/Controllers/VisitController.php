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
}
