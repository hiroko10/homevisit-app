<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\Client;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    // ==＝＝index:一覧表示(READ)＝==＝
    public function index(Request $request) {
        // 1. バリデーション(入力)チェック
        $request->validate([
        // client_idは必須(required)で、かつclientsテーブルのidに実在すること(exists)
        'client_id' => 'required|exists:clients,id',

        // sortは任意だが、送るなら 'visited_at' か 'is_favorite' のどちらかであること
        'sort' => 'nullable|in:visited_at,is_favorite',

        // orderも任意だが、送るなら 'asc' か 'desc' のどちらかであること
        'order' => 'nullable|in:asc,desc',
        ]);


        // 2. バリテーションチェックを通過したデータのみ使用し処理開始
        // JSの axios.get(`/api/visits?client_id=${id}...`) から IDとkeywordを受け取る
        $clientId = $request->query('client_id');
        $keyword = $request->query('keyword');
        $sort = $request->query('sort', 'visited_at'); // 第2引数はデフォルト値
        $order = $request->query('order', 'desc');

        // 【認可対応】指定されたclient_idがログインユーザー自身のものであるか確認
        // 他人のclient_idを指定された場合、ここで404エラーを投げてシャットアウト
        $client = $request->user()->clients()->findOrFail($clientId);

        // Modelに該当IDのデータを取ってきてと依頼
        $visits = $client->visits()
               ->search($keyword)  // Visit.phpのモデルで定義したスコープを呼び出す
               ->orderBy($sort, $order)
               ->paginate(10);

        // JSONでフロントエンドに送り返す
        return response()->json($visits);
    }

    // ====create:登録画面(表：入力画面を作る/HTMLを返す) (CREATE:準備)====
    public function create(Client $client){
        return view('visits.create', compact('client'));
    }

    // ====store:保存実行(裏：DBに書き込む) (CREATE:実行)====
    public function store(StoreVisitRequest $request){
        // 1. バリデーション
        $validated = $request->validated();

        // 💡【認可対応】
        // 保存前に、送られてきたclient_idが本当に自分の顧客のものかチェック
        $client = $request->user()->clients()->findOrFail($request->input('client_id'));

        // 2. 保存
        // URLからではなく、$requestの中からIDを取り出して保存
        $visit = $client->visits()->create([
            'client_id'  => $request->client_id,
            'visited_at' => $request->visited_at,
            'content'    => $request->content,
        ]);

        // 3. APIへの返事（JSON形式）
        return response()->json($visit, 201);
    }



    // ====edit:画面編集(表：入力画面を作る/HTMLを返す) (UPDATE:準備)====
    public function edit(Visit $visit){
        return view('visits.edit', compact('visit'));
    }

    // ====update:更新処理(裏：DBに書き込む) (UPDATE:実行)====
    public function update(UpdateVisitRequest $request, $id) {
        // 1. 該当する履歴を探す
        $visit = Visit::findOrFail($id);

        // 【認可対応】その訪問履歴の親（顧客）が、ログインユーザーのものであるかチェック
        if ($visit->client->user_id !== $request->user()->id) {
            abort(403, 'この操作は許可されていません。');
        }

        // 2. バリデーション（入力チェック）
        $validated = $request->validated();

        // 3. データを更新して保存
        $visit->update($validated);

        // 4. JSONで「成功」と返す
        return response()->json($visit);
    }


    // ====destroy:削除 (DELETE)====
    public function destroy(Request $request, $id) {
        try {
            // IDで検索（見つからなければ404を出す）
            $visit = Visit::findOrFail($id);

            // 【認可対応】その訪問履歴の親（顧客）が、ログインユーザーのものであるかチェック
            if ($visit->client->user_id !== $request->user()->id) {
                abort(403, 'この操作は許可されていません。');
            }

            $visit->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // 内部向け: ログに詳細を残す
            Log::error('訪問履歴の削除に失敗', ['id' => $id, 'error' => $e->getMessage()]);

            // ユーザー向け: 詳細を隠した汎用メッセージを返す
            return response()->json(['error' => '削除に失敗しました。'], 500);
        }
    }


    // 訪問履歴のお気に入りの状態切り替え ====Update====
    public function toggleFavorite(Request $request, $id) {
        // 1. 該当する履歴を探す
        $visit = Visit::findOrFail($id);

        // 【認可対応】その訪問履歴の親（顧客）が、ログインユーザーのものであるかチェック
        if ($visit->client->user_id !== $request->user()->id) {
            abort(403, 'この操作は許可されていません。');
        }

        // 2. バリデーション（入力チェック）
        $validated = $request->validate([
            'is_favorite' => 'required|boolean',
        ]);

        // 3. データを更新
        $visit->update([
            'is_favorite' => $validated['is_favorite'],
        ]);

        // 4. JSONで「成功」と返す
        return response()->json(['success' => true, 'visit' => $visit]);
    }










    // 音声入力
    public function speechToText(Request $request){
        // 1. フロントから送られてきた音声ファイルがあるかチェック
        if (!$request->hasFile('audio')){
            return response()->json(['error' => '音声ファイルが見つかりません。'], 400);
        }
        try {
            $audioFile = $request->file('audio');

            // 2. 音声データをGeminiが読める形式(Base64テキスト)へ変換
            $audioData = base64_encode(file_get_contents($audioFile->getRealPath()));
            $mimeType = 'audio/webm'; // 'audio/mp3'など

            // 3. Gemini APIの準備
            $apiKey = env('GEMINI_API_KEY');
            $model = 'gemini-2.5-flash';
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            // 4. Geminiへ送るリクエストボディの作成
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->retry(3, 2000)->post($url, [  //最大3回再施行、2秒待つ
                'contents' => [
                    [
                        'parts' => [
                            // 音声データ本体
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $audioData
                                ]
                            ],
                            // Geminiへのプロンプト
                            [
                                'text' => "添付された音声を、訪問記録として適切な、聞き取りやすい綺麗な日本語に文字起こししてください。
                                【処理のルール】
                                ・「あー」、「えっと」などの不要な言葉（フィラー）はすべて自動で削除してください。
                                ・音声で話された内容を「明確な事実のみ」に基づき、パッと一目で内容が伝わるように適切な箇条書き（行頭は「・」）に整形して出力してください。
                                ・「〜です」「〜ます」などの冗長な表現は省きつつも、不自然な体言止めは避け、記録として読みやすい自然な日本語（「〜とのこと」「〜を実施」など）で記述してください。

                                【出力のルール】
                                ・文字起こしおよび整形した結果のテキストだけを出力してください。
                                ・「はい、文字起こししました」などの余計な解説や挨拶、およびハルシネーションは一切不要です。"
                            ]
                        ]
                    ]
                ]
            ]);

            // 5. Geminiからの返事を解析
            if ($response->failed()) {
                // Gemini側が503を返した場合
                if ($response->status() === 503) {
                    return response()->json([
                        'error' => 'AIサーバーが混雑しています。少し時間をおいて再度お試しください。'
                    ], 503);
                }

                Log::error('Gemini APIエラー：'. $response->body());

                return response()->json([
                    'error' => $response->body()
                    ], $response->status());
            }

            $result = $response->json();

            // 文字起こしテキストを取り出す
            $transcription = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // 前後の余計な空白を削ってフロントへ返す
            return response()->json([
                'text' => trim($transcription)
            ]);
        } catch (\Exception $e) {
            Log::error('音声文字起こしシステムエラー：'. $e->getMessage());

            // Gemini混雑時
            if (str_contains($e->getMessage(), '503')) {
                return response()->json([
                    'error' => 'AIサーバーが混雑しています。少し時間をおいて再度お試しください。'
                ], 503);
            }

            // その他
            return response()->json([
                'error' => '通信エラーが発生しました。'
            ], 500);
        }
    }

}
