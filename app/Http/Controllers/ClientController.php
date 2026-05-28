<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     * ブラウザで/clientsの画面へアクセス時にweb.phpから呼ばれる
     */
    public function index(){
        return view('clients.index');
    }


    // JSからアクセスしたときにapi.phpから呼ばれるデータを返す
    public function apiIndex(Request $request) {
        // バリデーションチェック
        $validated = $request->validate([
            'sort' => 'nullable|in:updated_at,last_name_kana,is_favorite',
            'order' => 'nullable|in:asc,desc',
            'initial' => 'nullable|string|max:1', //あかさたなボタンからの文字（「あ」など1文字）を許可
        ]);

        // バリデーションチェック通過したデータのみ使用し処理開始
        // 1. パラメータの取得
        $keyword = $request->query('keyword');
        $initial = $request->query('initial'); //画面から送られてきた「あ」などの文字を取得
        $sort  = $validated['sort']  ?? 'updated_at'; // 送られてこなければ 'updated_at'
        $order = $validated['order'] ?? 'desc';       // 送られてこなければ 'desc'

        // 一覧: ログインユーザーの顧客のみ取得 ＋ キーワード検索
        $query = $request->user()->clients()->search($keyword);

        // 2.「あかさたな」ボタンが押されたら、頭文字検索
        if (!empty($initial)) {
            $query->searchByInitial($initial); // Modelに作ったScopeを呼び出す
        }

        // 3. ソート処理
        if ($sort === 'last_name_kana') {
            $query->sortByKana($order); //Model
        } else {
            $query->orderBy($sort, $order); //更新日・お気に入り等、普通の並び替え
        }

        // 4. 結果を返却
        return response()->json($query->paginate(10));
    }



    // Geminiで訪問履歴を要約するAPI
    public function summarizeVisits(Request $request, $id) {
        // 1. ログインユーザーの顧客であることを確認(セキュリティチェック)
        $client = $request->user()->clients()->findOrFail($id);

        // 2. 特定の顧客に紐づく全ての訪問履歴から詳細テキスト(content)だけを最新順で取得
        $contents = $client->visits()->orderBy('visited_at', 'desc')->pluck('content')->filter()->toArray();

        // 訪問履歴($contents)が一件も無い場合、メッセージを返して終了
        if (empty($contents)) {
            return response()->json(['summary' => 'まだ訪問履歴が登録されていません。']);
        }

        // 3. 過去の履歴を一つの文章にまとめる(改行とハイフンで挟んで合体)
        $historyText = implode("\n---\n", $contents);

        // 4. Gemini APIを呼び出す準備
        $apiKey = env('GEMINI_API_KEY');

        // Gemini 2.5 Flashを使用
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        // AIへのプロンプト (3.で合体させた文章$historyTextを一文にする）
        $prompt = "あなたは家庭訪問の記録を厳密に整理するアシスタントです。
            以下の【過去の訪問履歴の詳細】に記載されている「明確な事実のみ」を使って、この顧客の状況を100文字〜150文字程度でわかりやすく要約してください。

            【絶対厳守の禁止事項】
            ・記載されていない内容からの推測、予測、アドバイス（ハルシネーション）は「絶対に」含めないでください。
            ・書かれている事実の記述のみで要約を構成してください。
            ・冗長になるのを防ぐため、「〜です」「〜ます」ではなく、体言止めで表現してください。

            【過去の訪問履歴の詳細】\n" . $historyText;

            try {
            // 5. GoogleのAIサーバーへリクエストを送信
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            // 6. AIから返って来たテキストを抽出しフロントに返す
            if ($response->successful()) {
                $result = $response->json();
                // 1番目の候補の中の、中身の1番目の要素のテキストを取り出す(AIは複数候補を出してしまうため)
                $summary = $result['candidates'][0]['content']['parts'][0]['text'] ?? '要約の生成に失敗しました。';

                return response()->json(['summary' => trim($summary)]);
            }

            if ($response->status() === 429) {
                return response()->json([
                    'summary' => 'AI利用上限に達しました。時間をおいて再度お試しください。'
                ], 429);
            }

            if ($response->status() === 503) {
                return response()->json([
                    'summary' => 'AIサーバーが混雑しています。少し時間をおいて再度お試しください。'
                ], 503);
            }

            return response()->json([
                'summary' => 'AI通信エラーが発生しました。'
            ], $response->status());

            // 例外が起きた時$eの中に一時的に保存
            } catch (\Exception $e) {
                return response()->json([
                    'summary' => $e->getMessage()
                ], 500);
            }


    }








    // 特定のクライアント内の訪問履歴を検索するAPI
    public function apiSearchActivities(Request $request, $id) {
        $keyword = $request->query('keyword');

        // 1. そのクライアントが存在するか確認し、紐づく活動記録のクエリを開始
        // 更新・削除: ログインユーザーの顧客かどうか確認
        $client = $request->user()->clients()->findOrFail($id);
        // Clientモデルにactivitiesリレーションがある前提
        $query = $client->activities();

        // 2. キーワードがあれば絞り込み
        if (!empty($keyword)) {
            $query->where('content', 'like', "%{$keyword}%");
        }

        // 3. 取得して返却 --ページネーションを追加
        $results = $query->orderBy('created_at', 'desc')->paginate(20);
        return response()->json($results);
    }

    // 非同期型の保存
    public function apiStore(StoreClientRequest $request)
    {
        $validated = $request->validated();

        // ログインユーザーのIDを追加
        $validated['user_id'] = $request->user()->id;

        // 保存
        $client = Client::create($validated);

        // 結果を返す
        return new ClientResource(($client));
    }


    /**
     * Display the specified resource.
     */
    public function show() {
        return view('clients.show');
    }

    public function apiShow(Request $request, $id) {
        // 更新・削除: ログインユーザーの顧客かどうか確認
        $client = $request->user()->clients()->findOrFail($id);

        return new ClientResource(($client));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, $id) {
        // 更新・削除: ログインユーザーの顧客かどうか確認
        $client = $request->user()->clients()->findOrFail($id);

        $validated = $request->validated();

        $client->update($validated);

        return new ClientResource(($client));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function apiDestroy(Request $request, $id) {
        // 更新・削除: ログインユーザーの顧客かどうか確認
        $client = $request->user()->clients()->findOrFail($id);
        $client->delete();

        return response()->json(['message' => '削除しました']); //削除成功したことをJSONで返す
    }



    public function toggleFavorite(Request $request, $id) {
        // 更新・削除: ログインユーザーの顧客かどうか確認
        $client = $request->user()->clients()->findOrFail($id);

        // バリデーション（入力チェック）
        $validated = $request->validate([
            'is_favorite' => 'required|boolean',
        ]);
        // DBを更新
        $client->update(['is_favorite' => $validated['is_favorite']]);

        // 結果をJSON形式で返す
        return response()->json([
            'success' => true,
            'is_favorite' => $client->is_favorite
        ]);
    }


}
