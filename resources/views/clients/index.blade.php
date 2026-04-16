<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<div class="container">

    <h1 style="margin: 20px 20px 50px 20px;">訪問一覧</h1>

    {{-- +新規登録ボタン --}}
    <div style="display: flex; justify-content: flex-start; margin: 20px;">
        <button id="show-add-client-btn" onclick="enableAddClientForm()" style="background: #009688; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
        ＋ 新規登録
        </button>
    </div>

    {{-- +新規登録ボタン内 --}}
    <div id="add-client-container" style="display: none; background: #fbfeff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
        <h3 style="font-size: 1rem; margin-bottom: 20px;">新規訪問先登録</h3>

        <div style="margin-bottom: 15px;">
            <label style="font-size: 0.9rem; color: #555;">氏名：</label><br>
            <div style="display: flex; gap: 10px; margin-top: 5px;">
                <input type="text" id="new-client-last-name" placeholder="姓" style="flex: 1; min-width: 0; font-size:1.1rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
                <input type="text" id="new-client-first-name" placeholder="名" style="flex: 1; min-width: 0; font-size:1.1rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
            </div>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="font-size: 0.9rem; color: #555;">かな：</label><br>
            <div style="display: flex; gap: 10px; margin-top: 5px;">
                <input type="text" id="new-client-last-name-kana" placeholder="せい" style="flex: 1; min-width: 0; font-size:0.8rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
                <input type="text" id="new-client-first-name-kana" placeholder="めい" style="flex: 1; min-width: 0; font-size:0.8rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
            </div>
        </div>
        <div style="margin-bottom: 20px;">
            <label style="font-size: 0.9rem; color: #555;">特徴メモ：</label><br>
            <textarea id="new-client-memo" placeholder="どのような方かメモをご入力ください..." style="width: 100%; height: 80px; margin-top:5px; border-radius: 4px; border: 1px solid #ccc; padding: 8px; box-sizing: border-box;"></textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="addClient()" style="flex: 1; background: #009688; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">登録</button>
            <button type="button" onclick="cancelAddClientForm()" style="flex: 1; background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">キャンセル</button>
        </div>
    </div>


    {{-- 検索 --}}
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; margin: 40px 20px;">
        <div style="position: relative; flex: 1; display: flex; gap: 10px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #aaa;"></i>

            <input type="text" id="search-keyword" placeholder="名前やフリガナで検索" style="width: 100%; flex: 1; padding: 8px; border-radius: 8px; border: 1px solid #ccc;">
            <button onclick="getClients(1)" type="button" style="background: #7b7b7b; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                 検索
            </button>
        </div>
    </div>


    {{-- ソートボタン --}}
    <div style="margin-bottom: 15px; font-size: 0.9rem; color: #666; display: flex; align-items: center; gap: 10px;">
        <span>並び替え：</span>

        <button type="button" onclick="changeSort('last_name_kana')" style="background: none; border: 1px solid #ccc; padding: 5px 12px; border-radius: 20px; cursor: pointer;">
            名前
        </button>

        <button type="button" onclick="changeSort('updated_at')" style="background: #eee; border: 1px solid #ccc; padding: 5px 12px; border-radius: 20px; cursor: pointer;">
            最終訪問日
        </button>

        <button type="button" onclick="changeSort('is_favorite')" style="background: none; border: 1px solid #ccc; padding: 5px 12px; border-radius: 20px; cursor: pointer;">
            お気に入り
        </button>
    </div>

    {{-- 50音フィルター（あかさたな） --}}
    <div id="syllabary-filter" style="margin-bottom: 20px; font-size: 1.05rem; color: #666;">
        表示：
        <span class="kana-btn" onclick="filterByKana('あ')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">あ</span>
        <span class="kana-btn" onclick="filterByKana('か')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">か</span>
        <span class="kana-btn" onclick="filterByKana('さ')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">さ</span>
        <span class="kana-btn" onclick="filterByKana('た')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">た</span>
        <span class="kana-btn" onclick="filterByKana('な')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">な</span>
        <span class="kana-btn" onclick="filterByKana('は')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">は</span>
        <span class="kana-btn" onclick="filterByKana('ま')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">ま</span>
        <span class="kana-btn" onclick="filterByKana('や')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">や</span>
        <span class="kana-btn" onclick="filterByKana('ら')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">ら</span>
        <span class="kana-btn" onclick="filterByKana('わ')" style="cursor: pointer; color: #02afe4; margin-right: 10px;">わ</span>
    </div>





    {{-- JSで使うため（画面非表示） --}}
    <template id="client-template">
        <div class="client-row">
            {{-- <div class="client-avatar"> //画像つけたい時
                <i class="fa-solid fa-user"></i>
            </div> --}}

            <button type="button" class="fav-btn">☆</button>

            <div class="client-info">
                <span class="client-kana"></span>
                <a href="" class="client-link">
                    <span class="client-name"></span>
                </a>
            </div>

            <button type="button" class="client-delete-btn">削除</button>
        </div>
    </template>


    <hr>

    {{-- 実際にリストが表示 --}}
    <div id="client-list">読み込み中...</div>

    <div id="pagination-container"></div>

</div>

@if (session('message'))
    <p style="color: red;">{{ session('message') }}</p>
@endif

