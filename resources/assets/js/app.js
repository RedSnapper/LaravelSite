
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


const moveToFirstChild = (node,parent)=> {

    //We have the parent (in target).
    //If the parent has any children, we need to change the index to the id of the first child.
    //Otherwise we keep the parent, and have no index.
    if(parent.children.length) {
        api.moveBefore(node.id,parent.children[0].id);
    } else {
        api.moveInto(node.id,parent.id);
    }
};

$tree.bind(
    'tree.move',(e)=>{
        const moveInfo = e.move_info;
        const movedNode = moveInfo.moved_node;
        const targetNode = moveInfo.target_node;

        switch(moveInfo.position) {
            case 'before': {
                api.moveBefore(movedNode.id,targetNode.id);
            } break;
            case 'after': {
                api.moveAfter(movedNode.id,targetNode.id);
            } break;
            case 'inside': {
                moveToFirstChild(movedNode,targetNode);
            } break;
        }
    }

);

$tree.jqTreeContextMenu($('#myMenu'), {
    "rename": renameNode,
    "delete": deleteNode,
    "add": addNode
});



