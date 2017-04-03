
import './bootstrap';
import 'jqtree';
import './jqTreeContextMenu';

import * as api from './api/categories';


const $tree = $('#tree');

const deleteNode = (node)=>{

    api.removeCategory(node.id);

    $tree.tree('removeNode',node);
};

const addNode = node=> {

    const name = prompt("Name of category?");

    api.addCategory(node.id,name)
        .then(response=>{

            const data = response.data.data;

            $tree.tree('appendNode',{
                name:data.name,
                id: data.id
            },node);

            $tree.tree('openNode', node);
        });
};

const renameNode = node=> {

    const name = prompt("New name",node.name);

    api.renameCategory(node.id,name)
        .then(response=>{

            const data = response.data.data;
            $tree.tree('updateNode', node, data.name);

        });
};

$tree.tree({
    dragAndDrop: true,
    autoOpen: true,
    usecontextmenu: true
});

$tree.jqTreeContextMenu($('#myMenu'), {
    "rename": renameNode,
    "delete": deleteNode,
    "add": addNode
});



