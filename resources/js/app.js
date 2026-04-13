// ===============メイン指示===============
import axios from "axios";

import { fetchClient, fetchVisits, fetchClients, toggleClientFavorite } from "./api";
import { displayClientInfo, displayVisitList, getCurrentClientId, displayClientList } from "./ui";


// --- 訪問一覧ページ用 ---
let currentPage = 1;

window.getClients = async (page = 1) => {  //デフォルトを１ページにする
    currentPage = page;

    try {
        const keyword = document.getElementById("search-keyword")?.value || "";
        const pagination = await fetchClients(page, keyword);

        displayClientList(pagination.data);
        renderPaginationCommon(pagination, "pagination-container", (p) => getClients(p));
    } catch (error) {
        console.log("データの取得に失敗しました", error);
    }
}

// --お気に入り--
window.handleToggleFavorite = async (id, currentStatus) => {
    try {
        // サーバー側のデータ更新：現在のステータスを反転させてサーバーに送る (trueならfalse、falseならtrue)
         await toggleClientFavorite(id, !currentStatus);

         // 画面を書き換える
        if (document.getElementById("client-list")) {
            // --- 訪問一覧ページにいる場合 ---
            getClients(currentPage);
        } else if (document.getElementById("detail-fav-btn")) {
            // --- 詳細ページにいる場合 ---
            // displayClientInfoを直接呼ぶのではなく、すでにapp.jsにある詳細情報を取得して表示する関数を呼び直す
            getClientDetail(id);
        }

    } catch (error) {
        console.error("お気に入り更新失敗", error);
        alert("お気に入りの更新に失敗しました");
    }
};


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
        renderPaginationCommon(visitData, "visit-pagination-container", (p) => getClientDetail(id, p));
    } catch (error) {
        console.error("詳細データの取得に失敗しました", error);
    }
}



// ===============自動判定===============
// HTMLの読み込み終了後、URLの中身を確認しJS実行(詳細ページか一覧ページかを自動判定)
document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname;

    // ① 詳細ページの判定
    const detailMatch = path.match(/\/clients\/(\d+)/);
    if (detailMatch) {
        const id = detailMatch[1];
        console.log("詳細ページを表示します。ID:", id);
        return window.getClientDetail(id, 1); // 実行して終了
    }

    // ② 一覧ページの判定
    const isIndexPage = document.getElementById('client-list');
    if (isIndexPage) {
        console.log("一覧ページを表示します。");
        return getClients(); // 実行して終了
    }

    // ③ それ以外
    console.log("このページでは専用のJS処理はありません:", path);
});