
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

const moveNode = (moveInfo)=>{

    const movedNode = moveInfo.moved_node;
    const targetNode = moveInfo.target_node;

    switch(moveInfo.position) {
        case 'before': {
            return api.moveBefore(movedNode.id,targetNode.id);
        } break;
        case 'after': {
            return api.moveAfter(movedNode.id,targetNode.id);
        } break;
        case 'inside': {
            return moveToFirstChild(movedNode,targetNode);
        } break;
    }

    return new Promise();
};

const moveToFirstChild = (node,parent)=> {

    //We have the parent (in target).
    //If the parent has any children, we need to change the index to the id of the first child.
    //Otherwise we keep the parent, and have no index.
    if(parent.children.length) {
        return api.moveBefore(node.id,parent.children[0].id);
    } else {
        return api.moveInto(node.id,parent.id);
    }
};

const getAncestors  = (node,ancestors=[])=>{
    return !node.id ? ancestors : getAncestors(node.parent,ancestors.concat(node.id));
};

$tree.tree({
    dragAndDrop: true,
    usecontextmenu: true
});

const init = _=>{
    if($tree.data('selected')){

        const node = $tree.tree('getNodeById', $tree.data('selected'));
        const open_nodes = getAncestors(node);
        $tree.tree('setState',{open_nodes,selected_node:[node.id]});
    }
};



$tree.bind(
    'tree.move',(e)=>{

        e.preventDefault();

        const moveInfo = e.move_info;

        moveNode(moveInfo).then(response=>{
            e.move_info.do_move();
        });
    }
);

//TODO: use querystring merge rather than replace
$tree.bind('tree.click', e => {
    window.location.search = `?category=${e.node.id}`;
});

$tree.bind('tree.init',init);


$tree.jqTreeContextMenu($('#myMenu'), {
    "rename": renameNode,
    "delete": deleteNode,
    "add": addNode
});



