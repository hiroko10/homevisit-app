@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


<x-app-layout>
    <div class="container">


        {{-- 詳細画面の「<<一覧に戻る」リンクが、URLについている（from_page）を読み取って戻る --}}
        @php
            // URLの ?from_page=2 を読み取る、なければ 1 ページ目とする
            $fromPage = request('from_page', 1);
        @endphp

        <div style="margin-bottom: 20px;">
            {{-- 一覧に戻る際、受け取ったページ番号を ?page= として渡す --}}
            <a href="/clients?page={{ $fromPage }}" class="hover:text-[#0FA69D]" style="text-decoration: none; font-size: 0.9rem;">
                <i class="fa-solid fa-arrow-rotate-left ml-[24px] mr-[8px]"></i>一覧に戻る
            </a>
        </div>




        <h1 style="margin: 56px 20px 20px 20px" class="text-xl font-bold text-[#0FA69D] flex items-center mb-4">
            <i class="fa-solid fa-user mr-2 text-[1.1rem]"></i>
            個人ページ</h1>

        <div id="client-info" style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #eee;">
            {{-- 個人名の表示 --}}
            <div id="client-view-mode">
                <div style="font-size: 0.8rem; color: #888;" id="client-kana"></div>
                <div style="font-size: 1.5rem; font-weight: bold; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                    <span id="client-name"></span>
                    <button type="button" id="detail-fav-btn" style="border:none; background:none; cursor:pointer; font-size:1.5rem; line-height:1; color:#ccc; padding:0;">
                        ☆
                    </button>
                </div>
                <div class="break-all">特徴：<span id="client-memo"></span></div>
                <button onclick="enableClientEdit()" type="button" class="text-[rgb(50,159,255)] hover:text-[#1A80E6] hover:underline transition-colors duration-200" style="margin-top:10px; font-size:0.8rem; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-pen-to-square"></i>
                    基本情報を編集
                </button>
            </div>

            {{-- 個人情報の編集 --}}
            <div id="client-edit-mode" style="display: none; background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 30px;">
                <h2 style="font-size: 1rem; margin-bottom: 20px;">基本情報を編集</h2>

                <div style="margin-bottom: 10px;">
                    <label style="font-size: 0.9rem; color: #555;">名前(*必須)：</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <input type="text" id="edit-client-last-name" placeholder="姓" style="flex: 1; min-width: 0; font-size:1.2rem; font-weight:bold; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
                        <input type="text" id="edit-client-first-name" placeholder="名" style="flex: 1; min-width: 0; font-size:1.2rem; font-weight:bold; border-radius: 4px; border: 1px solid #ccc; padding: 8px;"><br>
                    </div>
                </div>

                <div style="margin-bottom: 10px;">
                    <label style="font-size: 0.9rem; color: #555;">かな(*必須)：</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <input type="text" id="edit-client-last-name-kana" placeholder="せい" style="flex: 1; min-width: 0; font-size:0.8rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;">
                        <input type="text" id="edit-client-first-name-kana" placeholder="めい" style="flex: 1; min-width: 0; font-size:0.8rem; border-radius: 4px; border: 1px solid #ccc; padding: 8px;"><br>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9rem; color: #555;">特徴：</label><br>
                    <div style="display: flex; gap: 10px; margin-top: 5px;">
                        <textarea id="edit-client-memo" style="width:100%; margin-top:10px; border-radius: 4px; border: 1px solid #ccc; padding: 8px; box-sizing: border-box; word-break: break-all;"></textarea>
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="updateClientInfo()" type="button" class="bg-[#0FA69D] hover:bg-[#13BEB4] transition-colors duration-200" style="flex: 1; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; flex: 1; font-weight: bold;">保存</button>
                    <button type="button" onclick="cancelClientEdit()" class="bg-[#6c757d] hover:bg-[#5a6268] duration-200" style="flex: 1; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">キャンセル</button>
                </div>
            </div>
        </div>


        <hr>

        {{-- 個人ページ内での訪問履歴の追加 --}}
        {{-- フォーム表示のボタン --}}
        <div style="display: flex; justify-content: flex-start; margin: 20px;">
            <button id="show-add-visit-btn" onclick="enableAddVisit()" type="button" 
                class="bg-[#0FA69D] hover:bg-[#13BEB4] transition-colors duration-200" 
                style="margin-bottom: 20px; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                ＋新規訪問履歴を追加
            </button>
        </div>

        <div id="add-visit-container" style="display:none; background: #ffffff; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin: 10px 0px 30px;">訪問履歴の追加</h3>
            <div style="margin-bottom: 20px;">
                <label>訪問日：</label><br>
                <input type="datetime-local" id="new-visit-at" style="border-radius: 8px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>訪問内容：</label><br>
                <textarea id="new-visit-content" rows="3" style="width: 100%; border-radius: 8px;" placeholder="訪問した内容をご入力ください..."></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="addVisit()" type="button" 
                    class="bg-[#0FA69D] hover:bg-[#13BEB4] transition-colors duration-200" style="flex: 1; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                    訪問履歴の保存
                </button>
                <button type="button" onclick="cancelAddVisit()" class="bg-[#6c757d] hover:bg-[#5a6268] duration-200" style="flex: 1; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                    キャンセル
                </button>
            </div>
        </div>


        {{-- 訪問履歴 --}}
        <h2 style="margin: 60px 20px 30px 20px" class="text-xl font-bold text-[#0FA69D] flex items-center mt-8 mb-4">
            <i class="fa-solid fa-clock-rotate-left mr-2 text-[1.1rem]"></i>
            訪問履歴</h2>


        {{-- AI要約 --}}
        <div class="px-4 sm:px-6 mt-4 mb-6">
            <div class="mb-2 flex items-center">
                {{-- タイトル全体を丸みのあるバックグラウンドに --}}
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-sm border border-emerald-200">
                    {{-- バッジの中に収まるようにアイコンのサイズと色を微調整 --}}
                    <svg class="w-3.5 h-3.5 text-emerald-600 animate-pulse" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 21l-.813-5.096L3 15l5.187-.813L9 9l.813 5.187L15 15l-5.187.813ZM18.079 18.625 18 21l-.079-2.375L15.5 18.5l2.421-.079L18 16l.079 2.421 2.375.079-2.375.079Zm1.125-11.813L19 9.5l-.125-2.688L16.125 6.75 19 6.625 19.125 4l.125 2.625 2.688.125-2.688.125Z" />
                    </svg>
                    訪問履歴AI要約
                </span>
            </div>

            {{-- AI要約文章ボックス --}}
            <div id="ai-summary-text" class="w-full min-h-[80px] p-4 bg-white border border-emerald-400 rounded-lg shadow-sm text-sm text-emerald-700 font-medium leading-relaxed transition-all duration-300">
                画面を読み込み中...
            </div>
        </div>


        {{-- 検索入力欄 --}}
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; margin: 40px 20px;">
            <div style="position: relative; flex: 1; display: flex; gap: 10px;">

                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; pointer-events: none;"></i>

                <input type="text" 
                    id="visit-search" 
                    placeholder="訪問内容を検索..." 
                    class="py-[8px] pr-[8px] pl-[38px]" 
                    style="font-size: 0.8em; width: 100%; flex: 1; border-radius: 8px; border: 1px solid #ccc;">

                <button onclick="getClientDetail(window.location.pathname.split('/').pop())"
                    type="button" class="bg-[#7b7b7b] hover:bg-[#919191] text-white font-bold py-[8px] px-[15px] rounded-[8px] border-none cursor-pointer white-space-nowrap transition-colors duration-200" style="white-space: nowrap;">
                    検索
                </button>
            </div>
        </div>






        {{-- 訪問履歴の並び替え --}}
        <div class="sort-section" style="margin-left: 20px; margin-bottom: 15px; font-size: 0.9rem; color: #666; display: flex; align-items: center; gap: 10px;">
            <span>並び替え：</span>
            {{-- <span style="margin-bottom: 15px; font-size: 0.9rem; color: #666; display: flex; align-items: center; gap: 10px; color: #666;">並び替え：</span> --}}
            <button type="button" id="sort-date-btn" onclick="changeVisitSort('visited_at')" class="active-sort hover:bg-[#0FA69D] hover:border-[#0FA69D] hover:text-white transition-colors duration-200" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 16px; cursor: pointer;">
                最終訪問日
            </button>
            <button type="button" id="sort-fav-btn" onclick="changeVisitSort('is_favorite')" class="hover:bg-[#0FA69D] hover:border-[#0FA69D] hover:text-white transition-colors duration-200" style="border: 1px solid #ccc; padding: 5px 12px; border-radius: 16px; cursor: pointer;">
                お気に入り
            </button>
        </div>



        {{-- 訪問履歴 --}}
        <div id="visit-list" style="margin: 10px;">履歴を読み込み中...</div>
        <div id="visit-pagination-container" style="margin-top: 20px; text-align: center;"></div>
        <br>


        {{-- 訪問履歴のテンプレート --}}
        <template id="visit-template">
            {{-- 訪問履歴の箱 --}}
            <div class="visit-item border border-gray-200 p-[10px] mb-[10px] rounded-[5px] bg-white shadow-sm">
                <div class="visit-row">

                    {{-- 表示モード --}}
                    <div class="view-mode">
                        <div class="flex items-center gap-[8px] mb-[8px]">
                            <button type="button" class="fav-visit-btn bg-transparent border-none cursor-pointer text-[1.5rem] text-gray-400 hover:scale-110 transition-transform duration-100">
                                ☆
                            </button>
                            {{-- 日時 --}}
                            <div class="text-[0.9em] text-gray-500">
                                日時：<span class="v-date font-medium"></span>
                            </div>
                        </div>
                        {{-- 内容 --}}
                        <div class="v-body my-[5px] text-gray-700 line-height-[1.5] text-lg py-2 break-all">
                            内容：<span class="v-content"></span>
                        </div>
                        {{-- ボタン --}}
                        <div class="v-actions flex gap-[10px] mt-[8px]">
                            <button type="button" class="edit-btn text-[0.8em] cursor-pointer border-none bg-transparent p-0 text-[#007bff] hover:text-[#0056b3] hover:underline transition-colors duration-200">
                                編集
                            </button>
                            <button type="button" class="visit-delete-btn text-[0.8em] cursor-pointer border-none bg-transparent p-0 text-red-600 hover:text-red-800 hover:underline transition-colors duration-200">
                                削除
                            </button>
                        </div>
                    </div>

                    {{-- 編集モード --}}
                    <div class="edit-mode">
                        {{-- 編集 --}}
                        <div class="mb-[8px]">
                            <label class="block text-[0.8em] text-gray-500 mb-[4px]">日時の編集：</label>
                            <input type="datetime-local" class="edit-at w-full rounded-[4px] border border-gray-300 p-[5px] focus:border-[#0FA69D] focus:outline-none">
                        </div>
                        <div class="mb-[8px]">
                            <label class="block text-[0.8em] text-gray-500 mb-[4px]">内容の編集：</label>
                            <textarea class="edit-content w-full rounded-[4px] border border-gray-300 p-[5px] focus:border-[#0FA69D] focus:outline-none" rows="3"></textarea>
                        </div>
                        {{-- ボタン --}}
                        <div class="text-right flex justify-end gap-[10px] mt-[12px]">
                            <button type="button" class="save-btn bg-[#0FA69D] hover:bg-[#13BEB4] text-white border-none py-[5px] px-[15px] rounded-[4px] cursor-pointer font-bold transition-colors duration-200">
                                保存
                            </button>
                            <button type="button" class="cancel-btn bg-gray-500 hover:bg-gray-600 text-white border-none py-[5px] px-[15px] rounded-[4px] cursor-pointer transition-colors duration-200">
                                キャンセル
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </template>







    </div>

    <script>
        const clientId = window.location.pathname.split('/').pop();
        console.log("取得したID：", clientId);
    </script>

</x-app-layout>