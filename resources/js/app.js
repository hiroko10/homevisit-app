import axios from 'axios';

async function getClients(){
    try {
        const response = await axios.get('/api/clients');
        const clients = response.data;
        
        const listContainer = document.getElementById('client-list');

        if (!listContainer) return;

        listContainer.innerHTML = '';

        clients.forEach(client => {
            const div = document.createElement('div');
            // 行全体のスタイル
            div.style.display = "flex";
            div.style.alignItems = "center";
            div.style.marginBottom = "10px";
            div.style.padding = "10px";
            div.style.borderBottom = "1px solid #eee";
            div.style.marginBottom = "15px";

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

window.deleteClient = async (id) => {
    if (!confirm('本当に削除しますか？')) return;
    try{
        await axios.delete(`/api/clients/${id}`);
            alert('削除しました');
            getClients();
        } catch (error) {
        alert('削除に失敗しました');
    }

    }

getClients();