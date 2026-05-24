<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. ログイン用ユーザーを自動作成
        \App\Models\User::create([
            'name' => 'ホスト',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // 50音を網羅したダミーデータのリスト
        $dummyClients = [
            ['last_name' => '浅田', 'first_name' => '真央', 'last_name_kana' => 'あさだ', 'first_name_kana' => 'まお', 'user_id' => 1],
            ['last_name' => '井上', 'first_name' => '雄彦', 'last_name_kana' => 'いのうえ', 'first_name_kana' => 'たけひこ', 'user_id' => 1],
            ['last_name' => '宇多田', 'first_name' => 'ヒカル', 'last_name_kana' => 'うただ', 'first_name_kana' => 'ひかる', 'user_id' => 1],
            ['last_name' => '遠藤', 'first_name' => '保仁', 'last_name_kana' => 'えんどう', 'first_name_kana' => 'やすひと', 'user_id' => 1],
            ['last_name' => '織田', 'first_name' => '信長', 'last_name_kana' => 'おだ', 'first_name_kana' => 'のぶなが', 'user_id' => 1],
            ['last_name' => '加藤', 'first_name' => '茶', 'last_name_kana' => 'かとう', 'first_name_kana' => 'ちゃ', 'user_id' => 1],
            ['last_name' => '木村', 'first_name' => '拓哉', 'last_name_kana' => 'きむら', 'first_name_kana' => 'たくや', 'user_id' => 1],
            ['last_name' => '工藤', 'first_name' => '新一', 'last_name_kana' => 'くどう', 'first_name_kana' => 'しんいち', 'user_id' => 1],
            ['last_name' => '玄田', 'first_name' => '哲章', 'last_name_kana' => 'げんだ', 'first_name_kana' => 'てっしょう', 'user_id' => 1],
            ['last_name' => '小林', 'first_name' => '幸子', 'last_name_kana' => 'こばやし', 'first_name_kana' => 'さちこ', 'user_id' => 1],
            ['last_name' => '佐藤', 'first_name' => '健', 'last_name_kana' => 'さとう', 'first_name_kana' => 'たける', 'user_id' => 1],
            ['last_name' => '白石', 'first_name' => '麻衣', 'last_name_kana' => 'しらいし', 'first_name_kana' => 'まい', 'user_id' => 1],
            ['last_name' => '鈴木', 'first_name' => '一郎', 'last_name_kana' => 'すずき', 'first_name_kana' => 'いちろう', 'user_id' => 1],
            ['last_name' => '瀬戸', 'first_name' => '大也', 'last_name_kana' => 'せと', 'first_name_kana' => 'だいや', 'user_id' => 1],
            ['last_name' => '反町', 'first_name' => '隆史', 'last_name_kana' => 'そりまち', 'first_name_kana' => 'たかし', 'user_id' => 1],
            ['last_name' => '高橋', 'first_name' => '一生', 'last_name_kana' => 'たかはし', 'first_name_kana' => 'いっせい', 'user_id' => 1],
            ['last_name' => '津田', 'first_name' => '梅子', 'last_name_kana' => 'つだ', 'first_name_kana' => 'うめこ', 'user_id' => 1],
            ['last_name' => '寺田', 'first_name' => '心', 'last_name_kana' => 'てらだ', 'first_name_kana' => 'こころ', 'user_id' => 1],
            ['last_name' => '徳川', 'first_name' => '家康', 'last_name_kana' => 'とくがわ', 'first_name_kana' => 'いえやす', 'user_id' => 1],
            ['last_name' => '中居', 'first_name' => '正広', 'last_name_kana' => 'なかい', 'first_name_kana' => 'まさひろ', 'user_id' => 1],
            ['last_name' => '西野', 'first_name' => '七瀬', 'last_name_kana' => 'にしの', 'first_name_kana' => 'ななせ', 'user_id' => 1],
            ['last_name' => '沼田', 'first_name' => '隊長', 'last_name_kana' => 'ぬまた', 'first_name_kana' => 'たいちょう', 'user_id' => 1],
            ['last_name' => '根本', 'first_name' => '要', 'last_name_kana' => 'ねもと', 'first_name_kana' => 'かなめ', 'user_id' => 1],
            ['last_name' => '野村', 'first_name' => '克也', 'last_name_kana' => 'のむら', 'first_name_kana' => 'かつや', 'user_id' => 1],
            ['last_name' => '長谷川', 'first_name' => '長治', 'last_name_kana' => 'はせがわ', 'first_name_kana' => 'ちょうじ', 'user_id' => 1],
            ['last_name' => '広瀬', 'first_name' => 'すず', 'last_name_kana' => 'ひろせ', 'first_name_kana' => 'すず', 'user_id' => 1],
            ['last_name' => '福山', 'first_name' => '雅治', 'last_name_kana' => 'ふくやま', 'first_name_kana' => 'まさはる', 'user_id' => 1],
            ['last_name' => '松本', 'first_name' => '人志', 'last_name_kana' => 'まつもと', 'first_name_kana' => 'ひとし', 'user_id' => 1],
            ['last_name' => '三浦', 'first_name' => '知良', 'last_name_kana' => 'みうら', 'first_name_kana' => 'かずよし', 'user_id' => 1],
            ['last_name' => '村上', 'first_name' => '春樹', 'last_name_kana' => 'むらかみ', 'first_name_kana' => 'はるき', 'user_id' => 1],
            ['last_name' => '矢沢', 'first_name' => '永吉', 'last_name_kana' => 'やざわ', 'first_name_kana' => 'えいきち', 'user_id' => 1],
            ['last_name' => 'ルフィ', 'first_name' => 'モンキー', 'last_name_kana' => 'るふぃ', 'first_name_kana' => 'もんきー', 'user_id' => 1],
            ['last_name' => '和田', 'first_name' => 'アキ子', 'last_name_kana' => 'わだ', 'first_name_kana' => 'あきこ', 'user_id' => 1],
        ];

        // 1. まず純粋に顧客だけ33人作成
        foreach ($dummyClients as $clientData) {
            Client::create($clientData);
        }

        // 2. 顧客が全員データベースに入りきった「あと」で、満を持して履歴シーダーを呼ぶ
        $this->call([
            VisitSeeder::class,
        ]);
    }
}