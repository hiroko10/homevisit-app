import axios from "axios";

// --- 一覧ページ用 ---
async function getClients() {
    try {
        const response = await axios.get("/api/clients");
        const clients = response.data; //データ配列

        //1 HTML側の箱を取得
        const listContainer = document.getElementById("client-list");

        if (!listContainer) return; //箱がない場合中断

        listContainer.innerHTML = ""; //読み込み中の文字等を削除

        //2 データの数だけループしてHTML作成
        clients.forEach((client) => {
            const div = document.createElement("div");
            // 行全体のスタイル
            div.style.display = "flex";
            div.style.alignItems = "center";
            div.style.marginBottom = "10px";
            div.style.padding = "10px";
            div.style.borderBottom = "1px solid #eee";
            div.style.marginBottom = "15px";

            // 名前とボタンをセット
            div.innerHTML = `
                <div style="display: flex; flex-direction: column;">
                    <span style="font-size: 0.7rem; color: #666; margin-bottom: -2px;">
                        ${client.last_name_kana} ${client.first_name_kana}
                    </span>
                    <a href="/clients/${client.id}" style="margin-right: auto; text-decoration: none; color: #333; font-weight: bold;">
                        ${client.last_name} ${client.first_name}
                    </a>
                </div>
                <button type="button" onclick="deleteClient(${client.id})"
                    style="background-color: #ff4d4f; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                    削除
                </button>
                `;
            listContainer.appendChild(div);
        });

        console.log("画面の組み立てが完了しました");
    } catch (error) {
        console.log("データの取得に失敗しました", error);
    }
}

// --- 詳細ページ用 ---

async function getClientDetail(id) {
    try {
        const response = await axios.get(`/api/clients/${id}`);
        const client = response.data;

        // ふりがなをセット
        document.getElementById("client-kana").innerText = `${client.last_name_kana} ${client.first_name_kana}`;
        //名前とメモ
        document.getElementById("client-name").innerText = `${client.last_name} ${client.first_name}`;
        document.getElementById("client-memo").innerText = client.memo || "なし";

        //訪問履歴リスト組み立て
        const visitList = document.getElementById("visit-list");
        visitList.innerHTML = ""; //ループの前に中身を空にする

        if (client.visits && client.visits.length > 0) {
            client.visits.forEach((visit) => { //ループ開始

                const formattedDate = visit.visited_at ? visit.visited_at.replace(' ', 'T') : '';

                const visitDiv = document.createElement("div");
                visitDiv.style.border = "1px solid #eee";
                visitDiv.style.padding = "10px";
                visitDiv.style.marginBottom = "10px";
                visitDiv.style.borderRadius = "5px";

                //訪問履歴編集->show.blade.phpへ
                visitDiv.innerHTML = `
                    <div id="visit-row-${visit.id}">
                            
                            <div class="view-mode">
                                <div style="font-size: 0.9em; color:#666;">日時：<span class="v-date">${visit.visited_at}</span></div>
                                <div style="margin: 5px 0">内容：<span class="v-content">${visit.content}</span></div>
                                <div style="display:flex; gap: 10px;">
                                    <button onclick="enableEdit(${visit.id})" style="font-size: 0.8em; color: #007bff; cursor: pointer; border: none; background: none; padding: 0;">編集</button>
                                    <button onclick="deleteVisit(${visit.id})" style="font-size:0.8em; color: red; cursor: pointer; border: none; background: none; padding: 0;">削除</button>
                                </div>
                            </div>

                            <div class="edit-mode" style="display: none; margin-top: 5px;">
                                <div style="margin-bottom: 8px;">
                                    <label style="font-size: 0.8em; color: #666;">日時の編集：</label><br>
                                    <input type="datetime-local" id="edit-at-${visit.id}" value="${formattedDate}" style="width: 100%; border-radius: 4px; border: 1px solid #ccc; padding: 5px;">
                                </div>
                                <div style="margin-bottom: 8px;">
                                    <label style="font-size: 0.8em; color: #666;">内容の編集：</label><br>
                                    <textarea id="edit-content-${visit.id}" rows="3" style="width: 100%; border-radius: 4px; border: 1px solid #ccc; padding: 5px;">${visit.content}</textarea>
                                </div>
                                <div style="text-align: right; gap: 10px; display: flex; justify-content: flex-end;">
                                    <button onclick="updateVisit(${visit.id})" style="background: #28a745; color: white; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 0.8em;">保存</button>
                                    <button onclick="cancelEdit(${visit.id})" style="background: #6c757d; color: white; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; font-size: 0.8em;">キャンセル</button>
                                </div>
                            </div>

                        </div>
                    `;
                visitList.appendChild(visitDiv);
            });
        } else {
            visitList.innerHTML = "訪問履歴はまだありません";
        }
    } catch (error) {
        console.error("詳細データの取得に失敗しました", error);
    }
}




// ---削除(グローバル関数に定義でどこからでも呼べる)---
window.deleteClient = async (id) => {
    if (!confirm("本当に削除しますか？")) return;
    try {
        await axios.delete(`/api/clients/${id}`); //APIに削除リクエスト
        alert("削除しました");
        getClients(); //再度一覧を取得し画面更新
    } catch (error) {
        alert("削除に失敗しました");
    }
};


window.deleteVisit = async (id) => {
    if (!confirm("この履歴を削除しますか？")) return;
    try {
        await axios.delete(`/api/visits/${id}`);
        alert("削除しました");

        // いま見ているページのURLから、クライアントIDを抜き出す
        const clientId = window.location.pathname.split("/").pop();

        // データを再取得して、画面を最新にする
        getClientDetail(clientId);
    } catch (error) {
        console.error("削除失敗", error);
    }
};



// --- 履歴の追加 ---
window.addVisit = async () => {
    const visitedAt = document.getElementById('new-visit-at').value;
    const content = document.getElementById('new-visit-content').value;
    
    // URLからIDを取得
    const clientId = window.location.pathname.split('/').pop();

    if (!visitedAt || !content) {
        alert("日時と内容を入力してください");
        return;
    }

    try {
        await axios.post('/api/visits', {
            client_id: clientId,
            visited_at: visitedAt,
            content: content
        });

        alert("訪問履歴を追加しました！");

        // 入力欄を空にする
        document.getElementById('new-visit-at').value = "";
        document.getElementById('new-visit-content').value = "";

        // 重要：再描画してリストを更新
        getClientDetail(clientId); 
        
    } catch (error) {
        console.error("追加失敗:", error);
        alert("保存に失敗しました。");
    }
};


// 新規登録

window.addClient = async () => {
    // 1. 入力値を取得
    const lastName = document.getElementById('new-client-last-name').value;
    const firstName = document.getElementById('new-client-first-name').value;
    const lastNameKana = document.getElementById('new-client-last-name-kana').value;
    const firstNameKana = document.getElementById('new-client-first-name-kana').value;
    const memo = document.getElementById('new-client-memo').value;

    // 名前がない場合は止める
    if (!lastName || !firstName) {
        alert("姓名どちらも入力してください");
        return;
    }

    try {
        // 2. APIにPOSTリクエストを送る（入力データをオブジェクト形式で渡す）
        await axios.post('/api/clients', {
            last_name: lastName,
            first_name: firstName,
            last_name_kana: lastNameKana,
            first_name_kana: firstNameKana,
            memo: memo
        });
        alert("新規登録しました！");

        // 3. 入力欄をクリア
        document.getElementById('new-client-last-name').value = "";
        document.getElementById('new-client-first-name').value = "";
        document.getElementById('new-client-memo').value = "";

        // 4. 一覧を最新の状態にする（すでに定義されているgetClientsを呼ぶ）
        getClients();

    } catch (error) {
        console.error("登録失敗:", error);
        alert("登録に失敗しました。");
    }
};



// --- 編集モードの切り替え制御 ---

window.enableEdit = function(id) {
    const container = document.getElementById(`visit-row-${id}`);
    container.querySelector('.view-mode').style.display = 'none';
    container.querySelector('.edit-mode').style.display = 'block';
};

window.cancelEdit = function(id) {
    const container = document.getElementById(`visit-row-${id}`);
    container.querySelector('.view-mode').style.display = 'block';
    container.querySelector('.edit-mode').style.display = 'none';
};



// --- 履歴の更新（保存） ---
window.updateVisit = async (id) => {
    const newDate = document.getElementById(`edit-at-${id}`).value;
    const newContent = document.getElementById(`edit-content-${id}`).value;

    if (!newDate || !newContent) {
        alert("日時と内容を入力してください");
        return;
    }

    try {
        // PUTメソッドでサーバーの api/visits/{id} にデータを送る
        await axios.put(`/api/visits/${id}`, {
            visited_at: newDate,
            content: newContent
        });

        alert("更新しました！");

        // 成功したら、URLからクライアントIDを取得して詳細画面を再描画する
        const clientId = window.location.pathname.split("/").pop();
        getClientDetail(clientId);

    } catch (error) {
        console.error("更新失敗:", error);
        alert("更新に失敗しました。");
    }
};





// HTMLの読み込み終了後、URLの中身を確認しJS実行(詳細ページか一覧ページかを自動判定)
document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;

    // --- 1. 詳細ページの判定 ---
    const detailMatch = path.match(/\/clients\/(\d+)/);
    if (detailMatch) {
        const id = detailMatch[1];
        console.log("詳細ページを表示します。ID:", id);
        return getClientDetail(id); // 実行して終了
    }

    // --- 2. 一覧ページの判定 ---
    const isIndexPage = document.getElementById('client-list');
    if (isIndexPage) {
        console.log("一覧ページを表示します。");
        return getClients(); // 実行して終了
    }

    // --- 3. それ以外 ---
    console.log("このページでは専用のJS処理はありません:", path);
});



