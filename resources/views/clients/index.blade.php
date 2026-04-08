<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<div class="container">

    <h1>訪問一覧</h1>

    {{-- +新規登録ボタン --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button id="show-add-client-btn" onclick="enableAddClientForm()" style="margin-bottom: 20px; background: #57b8ce; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
        ＋ 新規登録
        </button>
    </div>

    {{-- +新規登録ボタン内 --}}
    <div id="add-client-container" style="display: none; background: #fbfeff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
        <h3>新規登録</h3>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">

            <button id="show-add-client-btn" onclick="enableAddClientForm()" style="...">
            新規顧客を登録する
            </button>
        </div>


        <div style="margin-bottom: 10px;">
            <label>氏名：</label><br>
            <input type="text" id="new-client-last-name" placeholder="姓" style="border-radius: 4px">
            <input type="text" id="new-client-first-name" placeholder="名" style="border-radius: 4px">
        </div>
        <div style="margin-bottom: 10px;">
            <label>かな：</label><br>
            <input type="text" id="new-client-last-name-kana" placeholder="せい" style="border-radius: 4px">
            <input type="text" id="new-client-first-name-kana" placeholder="めい" style="border-radius: 4px">
        </div>
        <div style="margin-bottom: 15px;">
            <label>特徴メモ：</label><br>
            <textarea id="new-client-memo" placeholder="特徴メモ" style="width: 100%; border-radius:4px"></textarea>
        </div>

        <div style="text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
            <button type="button" onclick="addClient()" style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">登録する</button>
            <button type="button" onclick="cancelAddClientForm()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">キャンセル</button>
        </div>
    </div>


    {{-- 検索 --}}
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
        <div style="position: relative; flex: 1; display: flex; gap: 5px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #aaa;"></i>

            <input type="text" id="search-keyword" placeholder="名前やフリガナで検索" style="width: 100%; flex: 1; padding: 8px; border-radius: 8px; border: 1px solid #ccc;">
            <button onclick="getClients(1)" type="button" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                 検索
            </button>
        </div>
    </div>



    <hr>

    <div id="client-list">読み込み中...</div>

    <div id="pagination-container"></div>

</div>

@if (session('message'))
    <p style="color: red;">{{ session('message') }}</p>
@endif

