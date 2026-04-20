// ===============メイン指示===============
import axios from "axios";

import { fetchClient, fetchVisits, fetchClients, toggleClientFavorite, toggleVisitFavorite } from "./api";
import { displayClientInfo, displayVisitList, getCurrentClientId, displayClientList } from "./ui";


// --- 訪問一覧ページ用 ---
window.currentPage = 1;
window.currentSort = 'updated_at'; // デフォルトは更新順
window.currentOrder = 'desc';      // デフォルトは降順

window.getClients = async (page = 1) => {  //デフォルトを１ページにする
    window.currentPage = page;

    try {
        const keyword = document.getElementById("search-keyword")?.value || "";
        const pagination = await fetchClients(
            page, keyword, window.currentSort, window.currentOrder);

        displayClientList(pagination.data);
        renderPaginationCommon(pagination, "pagination-container", (p) => getClients(p));
    } catch (error) {
        console.log("データの取得に失敗しました", error);
    }
}

// 検索 ---ソートボタンが押された時---
window.changeSort = (sort) => {
    // 同じ項目が押されたら昇順・降順を反転させる
    if (window.currentSort === sort) {
        window.currentOrder = (window.currentOrder === 'asc') ? 'desc' : 'asc';
    } else {
        window.currentSort = sort;
        // 名前（kana）の時は昇順(asc)、日付の時は降順(desc)をデフォルト
        window.currentOrder = (sort === 'last_name_kana') ? 'asc' : 'desc';
    }

    // ソートを変えたら1ページ目から表示し直す
    getClients(1);
}


// --お気に入り(訪問一覧)--
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


// --- お気に入り (個人ページ・訪問履歴) ---
window.handleToggleVisitFavorite = async (visitId, currentStatus, clientId) => {
    try {
        // 1. サーバー側のデータを更新 (現在のステータスを反転させて送る)
        // ※ api.js に toggleVisitFavorite を作成・インポートしておく必要あり
        await toggleVisitFavorite(visitId, !currentStatus);

        // 2. 詳細画面を再読み込みして表示を更新
        // これにより、displayVisitList が再度走り、新しい星の状態が描画
        await getClientDetail(clientId, window.currentVisitPage || 1);

    } catch (error) {
        console.error("訪問履歴のお気に入り更新失敗", error);
        alert("訪問履歴のお気に入り更新に失敗しました");
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


// 一覧ページ（/clients）に戻ってきたとき、URLに ?page=2 と書いてあれば、自動的に2ページ目を読み込むように設定

window.onload = () => {
    // 1. URLの「?page=X」の部分をチェックする
    const urlParams = new URLSearchParams(window.location.search);
    const pageFromUrl = urlParams.get('page');

    if (document.getElementById("client-list")) {
        // 2. URLにページ指定があればそのページを、なければ1ページ目を表示
        const targetPage = pageFromUrl ? parseInt(pageFromUrl) : 1;
        getClients(targetPage);
    }
};




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