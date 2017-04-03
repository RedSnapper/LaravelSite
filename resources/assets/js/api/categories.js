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