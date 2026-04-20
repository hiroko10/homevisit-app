// ＝＝データ取得・通信用(fetch)＝＝

import axios from "axios";

// (人)特定client1名の基本情報を取得 - 個人ページ
export const fetchClient = async (id) => {
    const response = await axios.get(`/api/clients/${id}`);
    return response.data; // 全体のデータを返す
};

// 詳細ページの訪問履歴取得用
export const fetchVisits = async (clientId, page = 1, keyword = "", sort = 'visited_at', order = 'desc') => {
    const response = await axios.get(`/api/visits`, {
        params: {
            client_id: clientId,
            page: page,
            keyword: keyword,
            sort: sort,
            order: order
        }
    });
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

// お気に入り(訪問一覧)
export const toggleClientFavorite = async (id, isFavorite) => {
    // PUTリクエストでサーバーの値を更新する
    const response = await axios.put(`/api/clients/${id}/favorite`, {
        is_favorite: isFavorite
    });
    return response.data;
};

// お気に入り(個人ページ・訪問履歴)
export const toggleVisitFavorite = async (visitId, isFavorite) => {
    const response = await axios.patch(`/api/visits/${visitId}/favorite`, {
        is_favorite: isFavorite
    });
    return response.data;
};