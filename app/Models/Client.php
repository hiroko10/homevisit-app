<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = ['last_name', 'first_name', 'last_name_kana', 'first_name_kana', 'memo', 'is_favorite'];

    public function visits(){
    return $this->hasMany(Visit::class);
    }



    // 顧客一覧 -- キーワード検索
    public function scopeSearch($query, $keyword) {
        if (!empty($keyword)) {
            return $query->where(function($q) use ($keyword) {
                    $q->where('last_name', 'like', "%{$keyword}%")
                    ->orWhere('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name_kana', 'like', "%{$keyword}%")
                    ->orWhere('first_name_kana', 'like', "%{$keyword}%");
            });
        }
        //keywordが空だった場合、受け取ったクエリをそのまま返す
        return $query;
    }

    // 顧客一覧 -- 苗字・名前のフリガナ順に並べるScope
    // SQL -- CAST:置き換え  CHAR:純粋な文字(正確な五十音順に強制)
    public function scopeSortByKana($query, $order) {
        return $query->orderByRaw("CAST(last_name_kana AS CHAR) {$order}")
                     ->orderByRaw("CAST(first_name_kana AS CHAR) {$order}");
    }
}

