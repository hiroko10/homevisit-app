// 画面表示用(display)

// ===============共通ツール===============

// --クライアントIDを取得--
export const getCurrentClientId = () => window.location.pathname.split('/').pop();

// --顧客情報表示--
export const displayClientInfo = (client) => {
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

// --訪問一覧ページ--
export const displayClientList = (clients) => {
    const listContainer = document.getElementById("client-list");
    if (!listContainer) return;
    listContainer.innerHTML = "";

    clients.forEach((client) => {
        const template = document.getElementById("client-template").content.cloneNode(true);
        template.querySelector(".client-kana").innerText = `${client.last_name_kana} ${client.first_name_kana}`;
        template.querySelector(".client-name").innerText = `${client.last_name} ${client.first_name}`;
        template.querySelector(".client-link").href = `/clients/${client.id}`;
        template.querySelector(".client-delete-btn").onclick = () => deleteClient(client.id);
        listContainer.appendChild(template);
    });
};

// --ページネーション 一覧用・詳細用共通--
window.renderPaginationCommon = (data, containerId, onPageClick) => {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = "";
    container.style.display = "flex";
    container.style.gap = "10px";
    container.style.justifyContent = "center";

    // 「前へ」ボタン
    if (data.prev_page_url) {
        const prevBtn = document.createElement("button");
        prevBtn.innerText = "前へ";
        prevBtn.onclick = () => onPageClick(data.current_page - 1);
        container.appendChild(prevBtn);
    }

    // ページ情報
    const info = document.createElement("span");
    info.innerText = `${data.current_page} / ${data.last_page}`;
    container.appendChild(info);

    // 「次へ」ボタン
    if (data.next_page_url) {
        const nextBtn = document.createElement("button");
        nextBtn.innerText = "次へ";
        nextBtn.onclick = () => onPageClick(data.current_page + 1);
        container.appendChild(nextBtn);
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
        const clientId = getCurrentClientId();
        getClientDetail(clientId);
    } catch (error) {
        console.error("更新失敗:", error);
        alert("更新に失敗しました。");
    }
};


// --訪問履歴リスト表示--
export const displayVisitList = (visits, clientId) => {
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




// ===============ボタン操作 ＆ UI切り替え===============

// ---削除---
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

        getClientDetail(getCurrentClientId());

    } catch (error) {
        console.error("削除失敗", error);
    }
};



// --新規登録(訪問一覧ページ)--
window.addClient = async () => {
    // 入力値を取得
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
        // APIにPOSTリクエストを送る（入力データをオブジェクト形式で渡す）
        await axios.post('/api/clients', {
            last_name: lastName,
            first_name: firstName,
            last_name_kana: lastNameKana,
            first_name_kana: firstNameKana,
            memo: memo
        });
        alert("新規登録しました！");

        // 入力欄をクリア
        document.getElementById('new-client-last-name').value = "";
        document.getElementById('new-client-first-name').value = "";
        document.getElementById('new-client-memo').value = "";

        // 保存に成功したら、フォームを閉じてボタン表示に戻す
        cancelAddClientForm();

        // 一覧を最新の状態にする（すでに定義されているgetClientsを呼ぶ）
        getClients();

    } catch (error) {
        console.error("登録失敗:", error);
        alert("登録に失敗しました。");
    }
};


// -----訪問履歴(個人ページ)-----
// --- 訪問履歴の追加 ---
window.addVisit = async () => {
    const visitedAt = document.getElementById('new-visit-at').value;
    const content = document.getElementById('new-visit-content').value;

    // URLからIDを取得
    const clientId = getCurrentClientId();

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

        // 再描画してリストを更新
        getClientDetail(clientId);
    } catch (error) {
        console.error("追加失敗:", error);
        alert("保存に失敗しました。");
    }
};


// --個人ページの姓名・特徴メモの編集用--
// ① 表示/非表示の切り替え
window.enableClientEdit = () => {
    document.getElementById('client-view-mode').style.display = 'none';
    document.getElementById('client-edit-mode').style.display = 'block';
};

window.cancelClientEdit = () => {
    document.getElementById('client-view-mode').style.display = 'block';
    document.getElementById('client-edit-mode').style.display = 'none';
};

// ② データの更新処理（保存ボタン）
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

