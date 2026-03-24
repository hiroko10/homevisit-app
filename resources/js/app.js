import axios from 'axios';

// --- 一覧ページ用 ---
async function getClients(){
    try {
        const response = await axios.get('/api/clients');
        const clients = response.data;  //データ配列

        //1 HTML側の箱を取得
        const listContainer = document.getElementById('client-list');

        if (!listContainer) return; //箱がない場合中断

        listContainer.innerHTML = ''; //読み込み中の文字等を削除

        //2 データの数だけループしてHTML作成
        clients.forEach(client => {
            const div = document.createElement('div');
            // 行全体のスタイル
            div.style.display = "flex";
            div.style.alignItems = "center";
            div.style.marginBottom = "10px";
            div.style.padding = "10px";
            div.style.borderBottom = "1px solid #eee";
            div.style.marginBottom = "15px";

            // 名前とボタンをセット
            div.innerHTML = `
                <a href="/clients/${client.id}" style="margin-right: auto; text-decoration: none; color: #333; font-weight: bold;">
                    ${client.name}
                </a>
                <button type="button" onclick="deleteClient(${client.id})" 
                style="background-color: #ff4d4f; 
                color: white; 
                border: none; 
                padding: 5px 10px; 
                border-radius: 4px; 
                cursor: pointer;">
                削除
                </button>
                `;
                listContainer.appendChild(div);
        });

        console.log("画面の組み立てが完了しました");

    } catch (error) {
        console.log("データの取得に失敗しました", error);
    }
};





// --- 詳細ページ用 ---
async function getClients(){
    try {
        const response = await axios.get(`/api/clients/${id}`);
        const client = response.data;

        //名前とメモ
        document.getElementById('client-name').innerText = client.name;
        document.getElementById('client-memo').innerText = client.memo || 'なし';

        //訪問履歴追加リンク
        const addLinkContainer = document.getElementById('add-visit-link');
        addLinkContainer.innerHTML = `<a href="/clients/${client.id}/visits/create" style="color: blue; text-decoration: underline;">+訪問履歴を追加</a>`;

        //訪問履歴リスト組み立て
        const visitList = document.getElementById('visit-list');
        visitList.innerHTML = '';

        if (client.visits && client.visits.length > 0){
            client.visits.forEach(visit => {
                const visitDiv = document.createElement('div');
                visitDiv.style.border = "1px solid #eee";
                visitDiv.style.padding = "10px";
                visitDiv.style.marginBottom = "10px";
                visitDiv.style.borderRadius = "5px";

                visitDiv.style.innerHTML = `
                    <div style="font-size: 0.9em" color:#666;>日時：${visit.visited_at}</div>
                    <div style="margin: 5px 0">内容：${visit.content}</div>
                    <div style ="display:flex; gap: 10px;">
                        <a href="/visits/${visit.id}/edit" style="font-size: 0.8em">編集</a>
                        <button onclick="deleteVisit(${visit.id})" style="font-size:0.8em; color: red; cursor: pointer; border: none; background: none; padding: 0;">削除</button>
                    </div>
                    `;
                    visitList.appendChild(visitDiv);
            });
        } else {
            visitList.innerHTML = '訪問履歴はまだありません';
        }
    } catch (error) {
        console.error("詳細データの取得に失敗しました", error);
    }
}


// ---削除(グローバル関数に定義でどこからでも呼べる)---
window.deleteClient = async (id) => {
    if (!confirm('本当に削除しますか？')) return;
    try{
        await axios.delete(`/api/clients/${id}`); //APIに削除リクエスト
            alert('削除しました');
            getClients(); //再度一覧を取得し画面更新
        } catch (error) {
        alert('削除に失敗しました');
    }
    }


window.deleteVisit = async (id) => {
    if (!confirm('この履歴を削除しますか？')) return;
    try {
        //訪問履歴削除のAPI
        await axios.delete(`/api/visits/${id}`);
        alert('削除しました');

        const clientId = window.location.pathname.split('/').pop();
        getClientDetail(clientId);
    } catch (error) {
        console.error('削除失敗', error);
    }
}

// --- 実行処理（URLや条件に応じて動かす） ---

// 1. 一覧ページなら一覧を取得
const clientListContainer = document.getElementById('client-list');
if (clientListContainer) {
    getClients();
}

// 2. 詳細ページ（clientIdが定義されている場合）なら詳細を取得
if (typeof clientId !== 'undefined') {
    getClientDetail(clientId);
}