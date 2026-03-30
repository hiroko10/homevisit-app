<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['last_name', 'first_name', 'memo'];

    public function visits(){
    return $this->hasMany(Visit::class);
    }
}

