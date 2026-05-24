<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Visit;
use Carbon\Carbon;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // データベースに完全に登録済みの顧客をすべて安全に引き出す
        $clients = Client::all();

        // 厳選した5件の訪問トピック
        $visitContents = [
            1 => "本日は定期訪問。体調は概ね良好とのこと。最近お腹の調子が少し緩い日があったようだが、現在は完全に回復している。",
            2 => "血圧測定：128/82。安定。お薬の手帳を確認したところ、朝・晩ともに飲み忘れなくしっかりと自己管理できている。",
            3 => "スマートフォンの使い方が分からないとのことで、ご家族への連絡方法を一緒に練習。不安が解消され安心された様子だった。",
            4 => "ご近所の方と少し口論があったようで、不安そうな表情をされていた。傾聴に徹し、気持ちの落ち着きを最優先した。",
            5 => "次月の訪問スケジュールの確認。ご本人の希望日時を伺い、お持ちのカレンダーに予定をしっかりと書き込んでもらった。",
        ];

        foreach ($clients as $client) {
            // 1人につき5件の履歴を作成
            for ($i = 1; $i <= 5; $i++) {
                Visit::create([
                    'client_id'   => $client->id,
                    'user_id'     => 1,
                    // どっちが正解でも大丈夫なように、両方同時に指定
                    'visit_at'    => Carbon::now()->subDays(5 - $i)->setTime(10, 0, 0),
                    'visited_at'  => Carbon::now()->subDays(5 - $i)->setTime(10, 0, 0),

                    'content'     => $visitContents[$i],
                    'is_favorite' => false,
                ]);
            }
        }
    }
}