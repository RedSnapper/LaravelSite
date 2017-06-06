import 'jqtree';
import './jqTreeContextMenu';

import without from 'lodash/without';

let settings={};
let $tree;

const defaults = {
    deleteNode(node){},
    addNode(node,name){},
    renameNode(node,name){},
    moveBefore(node,target){},
    moveAfter(node,target){},
    moveInto(node,target){}
};

const init = (selector,options)=>{
    $tree = $(selector);

    addHandlers($tree);

    settings = $.extend(defaults,options);
};



const deleteNode = (node)=>{

    settings.deleteNode(node.id);

    $tree.tree('removeNode',node);
};

const addNode = node=> {

    const name = prompt("Name of category?");

    settings.addNode(node.id,name)
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

    settings.renameNode(node.id,name)
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
            return settings.moveBefore(movedNode.id,targetNode.id);
        } break;
        case 'after': {
            return settings.moveAfter(movedNode.id,targetNode.id);
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
        return settings.moveBefore(node.id,parent.children[0].id);
    } else {
        return settings.moveInto(node.id,parent.id);
    }
};

const getAncestors  = (node,ancestors=[])=>{
    return !node.id ? ancestors : getAncestors(node.parent,ancestors.concat(node.id));
};



const initialize = _=>{
    if($tree.data('selected')){

        const node = $tree.tree('getNodeById', $tree.data('selected'));
        const open_nodes = getAncestors(node);
        $tree.tree('setState',{open_nodes,selected_node:[node.id]});
    }
};

const addHandlers = $tree=>{

    $tree.tree({
        dragAndDrop: true,
        usecontextmenu: true
    });

    $tree.bind(
        'tree.move',(e)=>{

            e.preventDefault();

            const moveInfo = e.move_info;

            moveNode(moveInfo).then(response=>{
                e.move_info.do_move();
            });
        }
    );

    $tree.bind('tree.click', e => {

        const path = $tree.data('link');
        let parts = without(path.split('/'),"");
        parts = parts.concat(e.node.id);
        window.location.pathname = parts.join('/');
    });

    $tree.bind('tree.init',initialize);

    $tree.jqTreeContextMenu($('#myMenu'), {
        "rename": renameNode,
        "delete": deleteNode,
        "add": addNode
    });

};



export default init;
