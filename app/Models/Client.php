<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Client extends Model
{
    protected $fillable = ['name', 'memo'];

    public function visits(){
    return $this->hasMany(Visit::class);
    }
}

