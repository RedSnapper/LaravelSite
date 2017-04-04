import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
});

export const addCategory = (parent,name)=>{

    return api.post(`/categories`,{
        parent,
        name
    });
};

export const removeCategory = (id)=>{
    return api.delete(`/categories/${id}`);
};

export const renameCategory = (id,name)=>{
    return api.put(`/categories/${id}`,{
        name
    });
};

// export const moveTo = (id,parent = null,index = null)=>{
//     return api.put(`/categories/${id}/moveTo`,{
//         parent,index
//     });
// };

export const moveInto = (id,node)=>{
    return api.put(`/categories/${id}/moveInto`,{
        node
    });
};

export const moveBefore = (id,node)=>{
    console.log('moveBefore',node);
    return api.put(`/categories/${id}/moveBefore`,{
        node
    });
};

export const moveAfter = (id,node)=>{
    console.log('moveAfter',node);
    return api.put(`/categories/${id}/moveAfter`,{
        node
    });
};