<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes;

    protected $fillable = ['client_id', 'visited_at', 'content', 'memo'];

    public function client(){
    return $this->belongsTo(Client::class);
    }
}

