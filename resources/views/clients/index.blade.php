{{-- <head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head> --}}

<x-app-layout>
    <div class="container">

        <h1 style="margin: 20px 20px 50px 20px;" class="text-[1.5rem] font-extrabold text-[#0FA69D] flex items-center mb-4">
            <i class="fa-solid fa-users mr-2 text-[1.3rem]"></i>
            訪問先一覧
        </h1>

        {{-- +新規登録ボタン --}}
        <div style="display: flex; justify-content: flex-start; margin: 20px;">
            <button id="show-add-client-btn" onclick="enableAddClientForm()" class="bg-[#0FA69D] hover:bg-[#13BEB4] duration-200 text-white py-[10px] px-[20px] rounded-[8px] font-bold border-none cursor-pointer">
            ＋ 新規登録
            </button>
        </div>

        {{-- +新規登録ボタン内 --}}
        <div id="add-client-container" style="display: none; background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
            <h3 style="font-size: 1rem; margin-bottom: 20px;">新規訪問先登録</h3>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.9rem; color: #555;">氏名(*必須)：</label><br>
                <div style="display: flex; gap: 10px; margin-top: 5px;">
                    <input type="text" id="new-client-last-name" placeholder="姓" style="flex: 1; min-width: 0; font-size:1.1rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
                    <input type="text" id="new-client-first-name" placeholder="名" style="flex: 1; min-width: 0; font-size:1.1rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 0.9rem; color: #555;">かな(*必須)：</label><br>
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
                <button type="button" onclick="addClient()" class="flex-1 bg-[#0FA69D] hover:bg-[#13BEB4] duration-200 text-white font-bold py-[10px] px-[20px] rounded-[8px] border-none cursor-pointer">
                    登録登録</button>
                <button type="button" onclick="cancelAddClientForm()" class="flex-1 bg-[#6c757d] hover:bg-[#5a6268] duration-200 text-white py-[8px] px-[15px] rounded-[8px] border-none cursor-pointer">キャンセル</button>
            </div>
        </div>


        {{-- 検索 --}}
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; margin: 40px 20px;">
            <div style="position: relative; flex: 1; display: flex; gap: 10px;">

                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; pointer-events: none;"></i>

                <input type="text"
                    id="search-keyword"
                    placeholder="名前やふりがなでご検索ください"
                    class="py-[8px] pr-[8px] pl-[38px]"
                    style="font-size: 0.8em; width: 100%; flex: 1; border-radius: 8px; border: 1px solid #ccc;">

                <button onclick="getClients(1)" type="button" class="bg-[#7b7b7b] hover:bg-[#919191] text-white py-[8px] px-[15px] rounded-[8px] border-none cursor-pointer transition-colors duration-200">
                    検索
                </button>
            </div>
        </div>


        {{-- ソートボタン --}}
        <div style="margin-bottom: 15px; padding-left: 24px; font-size: 0.9rem; color: #666; display: flex; align-items: center; gap: 10px;">
            <span>並び替え：</span>

            <button type="button" onclick="changeSort('updated_at')" class="hover:bg-[#0FA69D] hover:border-[#0FA69D] hover:text-white transition-colors duration-200" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 16px; cursor: pointer;">
                更新日
            </button>

            <button type="button" onclick="changeSort('last_name_kana')" class="hover:bg-[#0FA69D] hover:border-[#0FA69D] hover:text-white transition-colors duration-200" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 16px; cursor: pointer;">
                名前
            </button>

            <button type="button" onclick="changeSort('is_favorite')" class="hover:bg-[#0FA69D] hover:border-[#0FA69D] hover:text-white transition-colors duration-200" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 16px; cursor: pointer;">
                お気に入り
            </button>
        </div>




        {{-- あかさたなナビゲーション --}}
        <div class="mx-[20px] mb-[25px] text-[0.9rem] text-gray-600 flex items-center gap-[8px] flex-wrap">
            <span class="text-gray-600 pl-1">表示：</span>

            {{-- 1.「すべて」ボタン（最初から選択状態のTailwindクラスをつけておく） --}}
            <button type="button" onclick="filterByInitial('', event)" class="initial-btn bg-[#0FA69D] text-white font-bold border-none px-4 py-1 cursor-pointer rounded-[4px] duration-200">
                すべて</button>

            {{-- 2. その他ボタン：クリックされたら JavaScript の filterByInitial() を呼び出す --}}
            <button type="button" onclick="filterByInitial('あ', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">あ</button>
            <button type="button" onclick="filterByInitial('か', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">か</button>
            <button type="button" onclick="filterByInitial('さ', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">さ</button>
            <button type="button" onclick="filterByInitial('た', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">た</button>
            <button type="button" onclick="filterByInitial('な', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">な</button>
            <button type="button" onclick="filterByInitial('は', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">は</button>
            <button type="button" onclick="filterByInitial('ま', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">ま</button>
            <button type="button" onclick="filterByInitial('や', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">や</button>
            <button type="button" onclick="filterByInitial('ら', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">ら</button>
            <button type="button" onclick="filterByInitial('わ', event)" class="initial-btn text-gray-600 border-none bg-transparent px-4 py-1 cursor-pointer rounded-[4px] duration-200">わ</button>
        </div>




        {{-- 訪問先一覧ページの顧客一覧：一人分のBOX枠組み --}}
        <template id="client-template">
            {{-- 1人分のBOX --}}
            <div class="client-row flex items-center p-[16px] border-b border-gray-100 mb-[10px] bg-white rounded-[8px] shadow-sm">

                {{-- お気に入りボタン（星マーク） --}}
                <button type="button" class="fav-btn border-none bg-transparent cursor-pointer text-[1.4rem] mx-[15px] pt-[18px] pr-[6px] pb-0 pl-0 text-gray-300 hover:scale-110 transition-transform duration-100">
                    ☆
                </button>

                {{-- 名前エリア（client-info） --}}
                <div class="client-info flex flex-col flex-grow">
                    {{-- ふりがな --}}
                    <span class="client-kana text-[0.75rem] text-gray-560 mb-[2px]"></span>

                    {{-- 氏名リンク --}}
                    <a href="" class="client-link no-underline group pr-[10px]">
                        <span class="client-name text-xl text-gray-560 font-bold group-hover:text-[#0FA69D] group-hover:underline transition-colors duration-200 flex items-center justify-between w-full"></span>
                    </a>
                </div>

                {{-- 削除ボタン --}}
                <button type="button" class="client-delete-btn bg-[#ff4d4f] hover:bg-[#e04345] text-white py-[6px] px-[12px] mr-3 rounded-[6px] border-none cursor-pointer text-[0.85rem] ml-[10px] transition-colors duration-200 font-bold">
                    削除
                </button>
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

</x-app-layout>