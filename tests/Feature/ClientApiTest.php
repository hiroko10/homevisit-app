<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    // 401：ログインしていないユーザーがアクセスした場合401になるか
    public function test_未承認状態で顧客一覧にアクセスすると401が返る(): void
    {
        // ログインしていない状態でAPIを叩く
        $response = $this->getJson('/api/clients');

        $response->assertStatus(401);
    }

    // 404：存在していない顧客IDにアクセス時404になるか
    public function test_存在しないIDを指定すると404が返る(): void
    {
        // テスト用のユーザーを一人作成しログイン状態にする
        /** @var \App\Models\User $user */ //
        $user = User::factory()->create();

        // ログインした状態で絶対に存在しないIDの詳細画面にアクセス
        $response = $this->actingAs($user)->getJson('/api/clients/9999');

        $response->assertStatus(404);
    }

    // 422：必須項目(姓名)を空で送った場合422になるか
    public function test_バリデーションエラー時に422が返る(): void
    {
        // テスト用のユーザーを作成しログイン
        $user = User::factory()->create();

        // 姓名をわざと空にしてデータを送る
        $invalidData = [
            'last_name' => '',
            'first_name' => '',
            'memo' => 'テストメモ'
        ];

        // ログインした状態で、空データをPOST
        /** @var \App\Models\User $user */ //
        $response = $this->actingAs($user)->postJson('/api/clients', $invalidData);

        $response->assertStatus(422);
    }


    public function test_正しいデータを送ると顧客が登録できる(): void
    {
        $user = User::factory()->create();

        $validData = [
            'last_name' => '山田',
            'first_name' => '太郎',
            'last_name_kana' => 'ヤマダ',
            'first_name_kana' => 'タロウ',
            'memo' => 'テスト'
        ];

        /** @var \App\Models\User $user */ //
        $response = $this->actingAs($user)->postJson('/api/clients', $validData);

        // 201（作成成功）が返ってくるかチェック
        $response->assertStatus(201);

        // データベースに本当に「山田 太郎」が存在するかチェック
        $this->assertDatabaseHas('clients', [
            'last_name' => '山田',
            'first_name' => '太郎'
        ]);
    }

}
