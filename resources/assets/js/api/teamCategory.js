import axios from 'axios';

const api = axios.create({
    baseURL: '/api/team',
});

export const addCategory = (team,parent,name)=>{

    return api.post(`${team}/category`,{
        parent,
        name
    });
};

export const removeCategory = (team,id)=>{
    return api.delete(`${team}/category/${id}`);
};

export const renameCategory = (team,id,name)=>{
    return api.put(`${team}/category/${id}`,{
        name
    });
};

export const moveInto = (team,id,node)=>{
    return api.put(`${team}/category/${id}/moveInto`,{
        node
    });
};

export const moveBefore = (team,id,node)=>{
    return api.put(`${team}/category/${id}/moveBefore`,{
        node
    });
};

export const moveAfter = (team,id,node)=>{
    return api.put(`${team}/category/${id}/moveAfter`,{
        node
    });
};