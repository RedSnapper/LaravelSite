import tree from './base';
import * as api from '../api/category';

const $tree = $('#tree');


const options = {

    deleteNode(node){
        return api.removeCategory(node);
    },

    addNode(node, name){
        return api.addCategory(node,name);
    },

    renameNode(node, name){
        return api.renameCategory(node,name);
    },

    moveBefore(node, target){
        return api.moveBefore(node,target);
    },

    moveAfter(node, target){
        return api.moveAfter(node,target);
    },

    moveInto(node, target){
        return api.moveInto(node,target);
    }

};


if ($tree.length) {
    tree($tree,options);
}