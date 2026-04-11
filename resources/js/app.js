import axios from "axios";

// --- 訪問一覧ページ用 ---
window.getClients = async (page = 1) => { //引数に page を追加（初期値は1）
    try {
        const keyword = document.getElementById("search-keyword")?.value || "";

        // URLの末尾にページ番号をくっつける
        const response = await axios.get(`/api/clients?page=${page}&keyword=${encodeURIComponent(keyword)}`);
        console.log("受け取ったデータ", response.data);
        const clients = response.data.data; //paginateを使うと response.data.data が配列に
        const pagination = response.data; // ページネーション情報全体を保存

        //1 HTML側の箱を取得
        const listContainer = document.getElementById("client-list");
        if (!listContainer) return; //箱がない場合中断
        listContainer.innerHTML = ""; //読み込み中の文字等を削除

        //2 データの数だけループしてHTML作成
        clients.forEach((client) => {
            // -1. templateをコピー
            const template = document.getElementById("client-template").content.cloneNode(true);

            // -2. コピーした中身（テキストやリンク）を差し替え
            const row = template.querySelector(".client-row"); // 一番外側のdiv
            template.querySelector(".client-kana").innerText = `${client.last_name_kana} ${client.first_name_kana}`;
            template.querySelector(".client-name").innerText = `${client.last_name} ${client.first_name}`;
            template.querySelector(".client-link").href = `/clients/${client.id}`;

            // -3. 削除ボタンの動作を設定
            template.querySelector(".client-delete-btn").onclick = () => deleteClient(client.id);

            // -4. 完成したものを画面（listContainer）に追加
            listContainer.appendChild(template);
        });
        renderPagination(pagination);

        console.log("画面の組み立てが完了しました");
    } catch (error) {
        console.log("データの取得に失敗しました", error);
    }
}



// --- 詳細ページ用 ---

window.getClientDetail = async (id, page = 1) => {
    try {
        const keyword = document.getElementById("visit-search")?.value || "";

        // ① データ取得
        const client = await fetchClient(id);
        const visitData = await fetchVisits(id, page, keyword);

        // ② 顧客情報表示
        displayClientInfo(client);

        // ③ 訪問履歴リスト表示
        displayVisitList(visitData.data, id);

        // ④ ページネーション表示
        renderVisitPagination(visitData, id);

    } catch (error) {
        console.error("詳細データの取得に失敗しました", error);
    }
}



// ① データ取得
const fetchClient = async (id) => {
    const response = await axios.get(`/api/clients/${id}`);
    return response.data;
};

const fetchVisits = async (id, page, keyword) => {
    const response = await axios.get(`/api/visits?client_id=${id}&page=${page}&keyword=${encodeURIComponent(keyword)}`);
    return response.data;
};



// ② 顧客情報表示
const displayClientInfo = (client) => {
    // 表示用
    document.getElementById("client-kana").innerText = `${client.last_name_kana} ${client.first_name_kana}`;
    document.getElementById("client-name").innerText = `${client.last_name} ${client.first_name}`;
    document.getElementById("client-memo").innerText = client.memo || "なし";

    // 編集用入力欄
    document.getElementById('edit-client-last-name').value = client.last_name;
    document.getElementById('edit-client-first-name').value = client.first_name;
    document.getElementById('edit-client-last-name-kana').value = client.last_name_kana;
    document.getElementById('edit-client-first-name-kana').value = client.first_name_kana;
    document.getElementById('edit-client-memo').value = client.memo || "";
};



// ③ 訪問履歴リスト表示
const displayVisitList = (visits, clientId) => {
    const visitList = document.getElementById("visit-list");
    visitList.innerHTML = "";

    if (!visits || visits.length === 0) {
        visitList.innerHTML = "訪問履歴はまだありません";
        return;
    }

    visits.forEach((visit) => {
        const template = document.getElementById("visit-template").content.cloneNode(true);
        const row = template.querySelector(".visit-row");
        row.id = `visit-row-${visit.id}`;

        // データの流し込み
        template.querySelector(".v-date").innerText = visit.visited_at;
        template.querySelector(".v-content").innerText = visit.content;

        // 編集フォームの準備
        const formattedDate = visit.visited_at ? visit.visited_at.replace(' ', 'T') : '';
        const editAt = template.querySelector(".edit-at");
        const editContent = template.querySelector(".edit-content");

        editAt.id = `edit-at-${visit.id}`;
        editAt.value = formattedDate;
        editContent.id = `edit-content-${visit.id}`;
        editContent.value = visit.content;

        // ボタンの動作割り当て
        template.querySelector(".edit-btn").onclick = () => enableEdit(visit.id);
        template.querySelector(".visit-delete-btn").onclick = () => deleteVisit(visit.id);
        template.querySelector(".save-btn").onclick = () => updateVisit(visit.id);
        template.querySelector(".cancel-btn").onclick = () => cancelEdit(visit.id);

        visitList.appendChild(template);
    });
};




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



// 新規登録(訪問一覧ページ)

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

        // 保存に成功したら、フォームを閉じてボタン表示に戻す
        cancelAddClientForm();

        // 4. 一覧を最新の状態にする（すでに定義されているgetClientsを呼ぶ）
        getClients();

    } catch (error) {
        console.error("登録失敗:", error);
        alert("登録に失敗しました。");
    }
};





// 訪問履歴(個人ページ)

// --- 訪問履歴の追加 ---
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

        // 保存が成功したらフォームを閉じてボタンを出す
        cancelAddVisit();

        // 重要：再描画してリストを更新
        getClientDetail(clientId); 
        
    } catch (error) {
        console.error("追加失敗:", error);
        alert("保存に失敗しました。");
    }
};





// --- 訪問履歴編集モードの切り替え ---

window.enableEdit = function(id) {
    const container = document.getElementById(`visit-row-${id}`);
    container.classList.add('is-editing');
};

window.cancelEdit = function(id) {
    const container = document.getElementById(`visit-row-${id}`);
    container.classList.remove('is-editing');
};



// --- 訪問履歴の更新（保存） ---
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


// 個人ページの姓名・特徴メモの編集用
// 1. 表示/非表示の切り替え
window.enableClientEdit = () => {
    document.getElementById('client-view-mode').style.display = 'none';
    document.getElementById('client-edit-mode').style.display = 'block';
};

window.cancelClientEdit = () => {
    document.getElementById('client-view-mode').style.display = 'block';
    document.getElementById('client-edit-mode').style.display = 'none';
};

// 2. データの更新処理（保存ボタン）
window.updateClientInfo = async () => {
    // 現在のページURLからIDを取得（例: /clients/8 -> 8）
    const id = window.location.pathname.split('/').pop();

    // 入力欄から最新の値を取得
    const data = {
        last_name: document.getElementById('edit-client-last-name').value,
        first_name: document.getElementById('edit-client-first-name').value,
        last_name_kana: document.getElementById('edit-client-last-name-kana').value,
        first_name_kana: document.getElementById('edit-client-first-name-kana').value,
        memo: document.getElementById('edit-client-memo').value,
    };

    try {
        // PUTリクエストを送信
        await axios.put(`/api/clients/${id}`, data);
        
        alert("基本情報を更新しました！");
        
        // データを再取得して表示を最新にする
        getClientDetail(id); 
        
        // 通常モードに戻す
        cancelClientEdit();
    } catch (error) {
        console.error("更新失敗:", error);
        alert("更新に失敗しました。");
    }
};




// --- 新規顧客登録フォームの表示制御 ---

window.enableAddClientForm = () => {
    document.getElementById('show-add-client-btn').style.display = 'none'; // ボタンを隠す
    document.getElementById('add-client-container').style.display = 'block'; // フォームを出す
};

window.cancelAddClientForm = () => {
    document.getElementById('show-add-client-btn').style.display = 'block'; // ボタンを出す
    document.getElementById('add-client-container').style.display = 'none'; // フォームを隠す
    
    // 入力欄をクリア
    document.getElementById('new-client-last-name').value = "";
    document.getElementById('new-client-first-name').value = "";
    document.getElementById('new-client-last-name-kana').value = "";
    document.getElementById('new-client-first-name-kana').value = "";
    document.getElementById('new-client-memo').value = "";
};





// --- 訪問履歴追加フォームの表示制御 ---

window.enableAddVisit = () => {
    // ボタンを隠して、フォームを表示する
    document.getElementById('show-add-visit-btn').style.display = 'none';// ボタンを隠す
    document.getElementById('add-visit-container').style.display = 'block';// フォームを出す
};

window.cancelAddVisit = () => {
    // フォームを隠して、ボタンを再表示
    document.getElementById('show-add-visit-btn').style.display = 'block';// ボタンを出す
    document.getElementById('add-visit-container').style.display = 'none';// フォームを隠す

    // 入力欄をクリア
    document.getElementById('new-visit-at').value = "";
    document.getElementById('new-visit-content').value = "";
};



// ページネーション

window.renderPagination = (data) => {
    const container = document.getElementById("pagination-container");
    if (!container) return; // 箱がなければ終了

    container.innerHTML = ""; // 前のボタンを一旦消す
    container.style.marginTop = "20px";
    container.style.display = "flex";
    container.style.gap = "10px";
    container.style.justifyContent = "center";
    container.style.alignItems = "center";

    // --- 「前へ」ボタン ---
    if (data.prev_page_url) {
        const prevBtn = document.createElement("button");
        prevBtn.innerText = "前へ";
        prevBtn.style.padding = "5px 15px";
        prevBtn.style.cursor = "pointer";
        prevBtn.onclick = () => getClients(data.current_page - 1); // 1つ前のページを読み直す
        container.appendChild(prevBtn);
    }

    // --- 現在のページ情報 ---
    const pageInfo = document.createElement("span");
    pageInfo.innerText = `${data.current_page} / ${data.last_page}`;
    pageInfo.style.fontWeight = "bold";
    container.appendChild(pageInfo);

    // --- 「次へ」ボタン ---
    if (data.next_page_url) {
        const nextBtn = document.createElement("button");
        nextBtn.innerText = "次へ";
        nextBtn.style.padding = "5px 15px";
        nextBtn.style.cursor = "pointer";
        nextBtn.onclick = () => getClients(data.current_page + 1); // 1つ次のページを読み直す
        container.appendChild(nextBtn);
    }
};





// 訪問履歴専用のページネーションボタン生成
window.renderVisitPagination = (data, clientId) => {
    const container = document.getElementById("visit-pagination-container");
    if (!container) return;

    container.innerHTML = ""; 
    // --- スタイル設定 ---
    container.style.marginTop = "20px";
    container.style.display = "flex";
    container.style.gap = "10px";
    container.style.justifyContent = "center";
    container.style.alignItems = "center";

    if (data.prev_page_url) {
        const prevBtn = document.createElement("button");
        prevBtn.innerText = "前へ";
        prevBtn.style.padding = "5px 15px";
        prevBtn.style.cursor = "pointer";
        // クリック時に getClientDetail を呼ぶ
        prevBtn.onclick = () => getClientDetail(clientId, data.current_page - 1);
        container.appendChild(prevBtn);
    }

    const pageInfo = document.createElement("span");
    pageInfo.innerText = `${data.current_page} / ${data.last_page}`;
    container.appendChild(pageInfo);

    if (data.next_page_url) {
        const nextBtn = document.createElement("button");
        nextBtn.innerText = "次へ";
        nextBtn.style.padding = "5px 15px";
        nextBtn.style.cursor = "pointer";
        // クリック時に getClientDetail を呼ぶ
        nextBtn.onclick = () => getClientDetail(clientId, data.current_page + 1);
        container.appendChild(nextBtn);
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
        return window.getClientDetail(id, 1); // 実行して終了
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



