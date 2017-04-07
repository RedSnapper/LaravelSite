import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
});

export const addCategory = (parent,name)=>{

    return api.post(`/category`,{
        parent,
        name
    });
};

export const removeCategory = (id)=>{
    return api.delete(`/category/${id}`);
};

export const renameCategory = (id,name)=>{
    return api.put(`/category/${id}`,{
        name
    });
};

export const moveInto = (id,node)=>{
    return api.put(`/category/${id}/moveInto`,{
        node
    });
};

export const moveBefore = (id,node)=>{
    return api.put(`/category/${id}/moveBefore`,{
        node
    });
};

export const moveAfter = (id,node)=>{
    return api.put(`/category/${id}/moveAfter`,{
        node
    });
};