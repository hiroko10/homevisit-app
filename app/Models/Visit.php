<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'visited_at',
        'content',
        'memo',
        'is_favorite',
    ];

    public function client(){
    return $this->belongsTo(Client::class);
    }


    /**
     * 訪問内容でキーワード検索するスコープーDBの検索ロジック
     */
    public function scopeSearch($query, $keyword)
    {
        // キーワードが空でなければ、検索条件を追加して返す
        if (!empty($keyword)) {
            return $query->where('content', 'like', "%{$keyword}%");
        }

        // 空なら、何もしないでそのまま返す
        return $query;
    }
}

