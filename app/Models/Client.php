<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [ 'user_id', 'last_name', 'first_name', 'last_name_kana', 'first_name_kana', 'memo', 'is_favorite'];


    // あかさたなボタン用の翻訳マップ（ひらがな版）
    const KANA_MAP = [
        'あ' => ['あ', 'い', 'う', 'え', 'お'],
        'か' => ['か', 'き', 'く', 'け', 'こ', 'が', 'ぎ', 'ぐ', 'げ', 'ご'], // 濁音も含める
        'さ' => ['さ', 'し', 'す', 'せ', 'そ', 'ざ', 'じ', 'ず', 'ぜ', 'ぞ'],
        'た' => ['た', 'ち', 'つ', 'て', 'と', 'だ', 'ぢ', 'づ', 'で', 'ど', 'っ'], // 「っ」なども考慮
        'な' => ['な', 'に', 'ぬ', 'ね', 'の'],
        'は' => ['は', 'ひ', 'ふ', 'へ', 'ほ', 'ば', 'び', 'ぶ', 'べ', 'ぼ', 'ぱ', 'ぴ', 'ぷ', 'ぺ', 'ぽ'],
        'ま' => ['ま', 'み', 'む', 'め', 'も'],
        'や' => ['や', 'ゆ', 'よ', 'ゃ', 'ゅ', 'ょ'],
        'ら' => ['ら', 'り', 'る', 'れ', 'ろ'],
        'わ' => ['わ', 'を', 'ん'],
    ];


    public function user(){
        return $this->belongsTo(User::class);
    }

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



    /**
     * あかさたな（頭文字）での絞り込みスコープ
     */
    public function scopeSearchByInitial($query, $initial)
    {
        // もし「あ」〜「わ」以外の変な文字が来たり、空っぽなら何もせずスルー
        if (empty($initial) || !array_key_exists($initial, self::KANA_MAP)) {
            return $query;
        }

        // 選択された行のひらがなリストを取得（例：「あ」なら ['あ','い','う','え','お']）
        $targets = self::KANA_MAP[$initial];

        // データベースに対して「どれかに前方一致（LIKE 'あ%'）するもの」を探す命令
        return $query->where(function ($q) use ($targets) {
            foreach ($targets as $char) {
                // OR で条件を繋いでいく (例: last_name_kana LIKE 'あ%' OR last_name_kana LIKE 'い%' ...)
                $q->orWhere('last_name_kana', 'LIKE', $char . '%');
            }
        });
    }



}

