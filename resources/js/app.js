import axios from 'axios';

async function getClients(){
    try {
        const response = await axios.get('/api/clients');
        console.log("APIから取得したデータ一覧：", response.data);

        if(response.data.length > 0){
            console.log("一人目の名前：", response.data[0].name);
        }
    } catch (error) {
        console.log("データの取得に失敗しました", error);
    }
};

// alert('Viteが動いています！');
getClients();