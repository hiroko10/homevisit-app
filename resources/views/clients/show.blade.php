@vite(['resources/css/app.css', 'resources/js/app.js'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container">

    <h1>個人ページ</h1>

    <div id="client-info" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
        {{-- 個人名の表示 --}}
        <div id="client-view-mode">
            <div style="font-size: 0.8rem; color: #888;" id="client-kana"></div>
            <div style="font-size: 1.5rem; font-weight: bold; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                <span id="client-name"></span>
                <button type="button" id="detail-fav-btn" style="border:none; background:none; cursor:pointer; font-size:1.5rem; line-height:1; color:#ccc; padding:0;">
                    ☆
                </button>
            </div>
            <div>特徴：<span id="client-memo"></span></div>
            <button onclick="enableClientEdit()" style="margin-top:10px; font-size:0.8rem; color: rgb(50, 159, 255); background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-pen-to-square"></i>
                基本情報を編集
            </button>
        </div>

        {{-- 個人名の編集 --}}
        <div id="client-edit-mode" style="display: none;">
            <h2>基本情報を編集</h2>
            <label>ふりがな：</label><br>
            <input type="text" id="edit-client-last-name-kana" placeholder="せい" style="font-size:0.7rem; border-radius: 8px;">
            <input type="text" id="edit-client-first-name-kana" placeholder="めい" style="font-size:0.7rem; border-radius: 8px;"><br>
            <label>名前：</label><br>
            <input type="text" id="edit-client-last-name" placeholder="姓" style="font-size:1.2rem; font-weight:bold; border-radius: 8px;">
            <input type="text" id="edit-client-first-name" placeholder="名" style="font-size:1.2rem; font-weight:bold; border-radius: 8px;"><br>
            <label>特徴：</label><br>
            <textarea id="edit-client-memo" style="width:100%; margin-top:10px; border-radius: 8px;"></textarea>
            
            <div style="margin-top:10px;">
                <button type="button" onclick="updateClientInfo()" style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">保存</button>
                <button type="button" onclick="cancelClientEdit()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">キャンセル</button>
            </div>
        </div>
    </div>


    <hr>

    {{-- 個人ページ内での訪問履歴の追加 --}}
    {{-- フォーム表示のボタン --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button id="show-add-visit-btn" onclick="enableAddVisit()" style="margin-bottom: 20px; background: #57b8ce; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
        ＋新規訪問履歴を追加
        </button>
    </div>

    <div id="add-visit-container" style="display:none; background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h3>訪問履歴の追加</h3>
        <div style="margin-bottom: 10px;">
            <label>訪問日：</label><br>
            <input type="datetime-local" id="new-visit-at" style="border-radius: 8px;">
        </div>
        <div style="margin-bottom: 10px;">
            <label>内容：</label><br>
            <textarea id="new-visit-content" rows="3" style="width: 100%; border-radius: 8px;" placeholder="訪問した内容をご入力"></textarea>
        </div>
        <button type="button" onclick="addVisit()" style="background: #57b8ce; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
            訪問履歴の保存
        </button>
        <button type="button" onclick="cancelAddVisit()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
            キャンセル
        </button>
    </div>


    {{-- 検索入力欄 --}}
    <div style="margin-bottom: 20px; display: flex; gap: 8px; width: 100%;">
        <input type="text" id="visit-search" placeholder="訪問内容を検索..."
            style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; flex: 1; font-size: 1rem; outline: none;">

        <button onclick="getClientDetail(window.location.pathname.split('/').pop())"
            style="padding: 8px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; white-space: nowrap; font-weight: bold;">
            検索
        </button>
    </div>



    {{-- JSで使用 --}}
    <template id="visit-template">
        <div class="visit-item">
            <div class="visit-row">
                {{-- 表示モード --}}
                <div class="view-mode">
                    <div class="v-meta">日時：<span class="v-date"></span></div>
                    <div class="v-body">内容：<span class="v-content"></span></div>
                    <div class="v-actions">
                        <button type="button" class="edit-btn">編集</button>
                        <button type="button" class="visit-delete-btn">削除</button>
                    </div>
                </div>

                {{-- 編集モード（最初はCSSで隠れる） --}}
                <div class="edit-mode">
                    <div class="edit-group">
                        <label>日時の編集：</label>
                        <input type="datetime-local" class="edit-at">
                    </div>
                    <div class="edit-group">
                        <label>内容の編集：</label>
                        <textarea class="edit-content" rows="3"></textarea>
                    </div>
                    <div class="edit-actions">
                        <button type="button" class="save-btn">保存</button>
                        <button type="button" class="cancel-btn">キャンセル</button>
                    </div>
                </div>
            </div>
        </div>
    </template>





    {{-- 訪問履歴 --}}
    <h2>訪問履歴</h2>
    <div id="visit-list">履歴を読み込み中...</div>

    <div id="visit-pagination-container" style="margin-top: 20px; text-align: center;"></div>

    <br>

    <a href="/clients">一覧に戻る</a>

</div>

<script>
    const clientId = window.location.pathname.split('/').pop();
    console.log("取得したID：", clientId);
</script>