
import './bootstrap';
import 'jqtree';
import './jqTreeContextMenu';
import Echo from 'laravel-echo';

import * as api from './api/categories';

import Pusher from 'pusher-js';
window.Pusher = Pusher;


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

$tree.bind(
    'tree.move',(e)=>{
        const moveInfo = e.move_info;

        const movedNode = moveInfo.moved_node;
        const targetNode = moveInfo.target_node;

        //api.moveBefore(movedNode.id,targetNode.id);

    }

);

$tree.jqTreeContextMenu($('#myMenu'), {
    "rename": renameNode,
    "delete": deleteNode,
    "add": addNode
});

const echo = new Echo({
    broadcaster: 'pusher',
    key: '9e4086f49da43ef0ba99',
    cluster: 'eu',
    encrypted: true
});

if($tree.length){

    echo.channel('category')
        .listen('CategoryCreated', (e) => {

            const node = $tree.tree('getNodeById', e.parent);

            $tree.tree('appendNode',{
                name:e.name,
                id: e.id
            },node);

        });
}
