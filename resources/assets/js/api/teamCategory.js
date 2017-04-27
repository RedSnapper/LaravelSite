import axios from 'axios';

const api = axios.create({
    baseURL: '/api/team',
});

export const removeCategory = (team,nodeId)=>{
    console.assert(typeof nodeId == 'number',nodeId);
    return api.delete(`${team}/category/${nodeId}`);
};

export const addCategory = (team,parent,name)=>{
    return api.post(`${team}/category`,{
        parent,
        name
    });
};

export const renameCategory = (team,nodeId,name)=>{
    console.assert(typeof nodeId == 'number',nodeId);
    return api.put(`${team}/category/${nodeId}`,{
        name
    });
};

export const moveBefore = (team,nodeId,node)=>{
    console.assert(typeof nodeId == 'number',nodeId);
    return api.put(`${team}/category/${nodeId}/moveBefore`,{
        node
    });
};

export const moveAfter = (team,nodeId,node)=>{
    console.assert(typeof nodeId == 'number',nodeId);
    return api.put(`${team}/category/${nodeId}/moveAfter`,{
        node
    });
};

export const moveInto = (team,nodeId,node)=>{
    console.assert(typeof nodeId == 'number',nodeId);
    return api.put(`${team}/category/${nodeId}/moveInto`,{
        node
    });
};
