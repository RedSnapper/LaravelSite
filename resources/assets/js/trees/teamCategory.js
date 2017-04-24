import tree from './base';
import * as api from '../api/teamCategory';

const $tree = $('#treeTeam');
const team = $tree.data('team');

const options = {

    deleteNode(node){
        return api.removeCategory(team,node);
    },

    addNode(node, name){
        return api.addCategory(team,node,name);
    },

    renameNode(node, name){
        return api.renameCategory(team,node,name);
    },

    moveBefore(node, target){
        return api.moveBefore(team,node,target);
    },

    moveAfter(node, target){
        return api.moveAfter(team,node,target);
    },

    moveInto(node, target){
        return api.moveInto(team,node,target);
    }

};


if ($tree.length) {
    tree($tree,options);
}