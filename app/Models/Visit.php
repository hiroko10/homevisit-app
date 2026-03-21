<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = ['client_id', 'visited_at', 'content', 'memo'];

    public function client(){
    return $this->belongsTo(Client::class);
    }
}

