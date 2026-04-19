// ＝＝データ取得・通信用(fetch)＝＝

import axios from "axios";

// (人)特定client1名の基本情報を取得 - 個人ページ
export const fetchClient = async (id) => {
    const response = await axios.get(`/api/clients/${id}`);
    return response.data; // 全体のデータを返す
};

// (履歴)特定client1名に紐づく複数の訪問履歴リストの取得
export const fetchVisits = async (id, page, keyword) => {
    const response = await axios.get(`/api/visits?client_id=${id}&page=${page}&keyword=${encodeURIComponent(keyword)}`);
    return response.data;
};

// 顧客のリスト取得（ページネーションの情報も返す） - 訪問一覧ページ
export const fetchClients = async (page = 1, keyword = "", sort = 'updated_at', order = 'desc') => {
    const response = await axios.get('/api/clients', {
        params: {
                page: page,
                keyword: keyword,
                sort: sort,
                order: order
            }
        });
    return response.data;
};

// お気に入り
export const toggleClientFavorite = async (id, isFavorite) => {
    // PUTリクエストでサーバーの値を更新する
    const response = await axios.put(`/api/clients/${id}/favorite`, {
        is_favorite: isFavorite
    });
    return response.data;
};