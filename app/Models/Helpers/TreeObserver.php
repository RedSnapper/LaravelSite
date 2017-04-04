<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 29/03/2017 09:52
 */

namespace App\Models\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TreeObserver {
	/*
	 * Add "AbstractTree::observe(TreeObserver::class);" into AppServiceProvider:boot()
	 * These event handlers ensure the stability of the tree.
	 */

	public function updating(TreeInterface $node) {
		$org = $node->getOriginal(); //Get the original values of the node.
		$node->size = $org['size'];			 //Set the node to it's original size.
		if($node->index != $org['index'] || ($node->parent != $org['parent'] && !is_null($node->parent)) ) {
			$nodeClass = get_class($node);
			$orig = new $nodeClass; //This will be the mirror of the original.
			$orig->setRawAttributes($org);
			$originalIndex = $orig->index;
			$treeSize = $node->count();
			$nodeSize = $node->size;
			$earthSize = $treeSize - $nodeSize;
			$heaven = $treeSize + 1000;
			if (($node->index == $originalIndex) || (is_null($node->index))) { //we only have the parent.
				$node->index = $node->index($node->parent)->first()->nextchild; //now we have the parent's nextChild.
			} else { //moving by index. if index is same then we need to see if it's parent
				$node->parent = $node->index($node->index)->first()->parent;
			}
			//now both the index and parent are ready. Let's check that they have actually changed after all.
			if ($node->index != $originalIndex || $node->parent != $orig->parent) {   //drop through to new position
				$node->index = $node->index > $originalIndex ? $node->index - $nodeSize : $node->index; //while in heaven, we will change the size of earth.
				$node->parent = $node->parent > $originalIndex ? $node->parent - $nodeSize : $node->parent; //so our own reference points must be adjusted.
				if($node->index <= ($earthSize+1) && $node->parent <= $earthSize) { //when going right to the end of the tree, earthsize +1 is legal.
					//need to move the branch into the stratosphere.
					$indexOffset = $heaven + ($node->index - $originalIndex); //how many places we are moving. ie remove old index and add new index.
					$indexAdj = $indexOffset > 0 ? "+$indexOffset" : "$indexOffset";
					$parentAdj = $heaven + $node->parent;
					//send branch to heaven.
					Schema::disableForeignKeyConstraints();
					//The branch root's parent is different = it's the node->parent + heaven.
					$orig->descendants(true)->update(['index' => DB::raw("`index` $indexAdj"),'parent' => DB::raw("if(parent < $originalIndex,$parentAdj,parent $indexAdj)")]);
					Schema::enableForeignKeyConstraints();

					$this->adjustTree($orig,false); //downsize the tree (the branch has gone to heaven).
					$this->adjustTree($node,true);  //resize the tree to make space for the returning branch

					//return branch back to earth.
					Schema::disableForeignKeyConstraints();
					$node->newQuery()
						->where('index', '>=', $heaven + $node->index)
						->where('index', '<', $heaven + $node->index + $nodeSize)
						->update(['index' => DB::raw("`index` - $heaven"),'parent' => DB::raw("parent - $heaven")]);
					Schema::enableForeignKeyConstraints();
				} else {
					$node->index = $originalIndex;
					$node->parent = $orig->parent;
				}
			}
		}
		return true;
	}

	public function deleted(TreeInterface $node) {
		$this->adjustTree($node,false);
		return true;
	}
	public function creating(TreeInterface $node) {
		/**
		 * when we create a node we must either have:
		 * (1) a parent (and this node will be the last child)
		 * (2) a treewalk. It will inherit the current index position and nodes to it's right will be shifted.
		 */
		$node->size = 1; //The size of a new node is always going to be 1. so the nextchild will be index+1;
		$root = $node->index(1)->first();
		if(isset($root->index)) {
			if(!isset($node->index) || ($node->index > $root->nextchild) || ($node->index < 1)) {
				if(!isset($node->parent)) {
					$parent = $root;
				} else {
					$parent = $node->index($node->parent)->first();
				}
				$node->parent = $parent->index;
				$node->index = $parent->nextchild;
			} else {
				//new tree.
				$curr = $node->index($node->index)->first();
				$node->parent = $curr->parent;
				$node->index = $curr->index;
			}
			$this->adjustTree($node,true); //inserting
		} else {
			$node->index = 1;
			$node->parent = null;
		}
		return true;
	}

	/**
	 * Adjust the tree for the insertion of a node or branch.
	 * How to work out what needs changing..
	 * (1) all the index >= the new index need to be incremented (this is our ordinal sequence).
	 * (2) the size of all ancestors is incremented. (this is a virtually done, as it's wrapped into the next point).
	 * (3) the revised size+index of those nodes which have changed needs to be updated.
	 * I.e. nodes to the right have their nextchild and index incremented while ancestors have their nextchild incremented.
	 * We have to be careful here because the right-branch of our immediate sibling to the left has the same nextchild as our
	 *  parent - and we don't want that changing.
	 *
	 * @param Category $node - must have parent/index set correctly. This is where the new node/branch will be placed.
	 * @param int  $size - the size of the branch that is being inserted.
	 */
	private function adjustTree(TreeInterface $node,bool $inserting) {
		if(isset($node->index) && isset($node->size)) {
			$adj = ($inserting ? "+ " : "- ") .$node->size; //insertion or deletion
			//ancestors have size adjusted. We need to force parent here because it's current nextChild is us and we don't want our left siblings.
			$node->newQuery()->ancestors(false)->update(['size' => DB::raw("size $adj")]);
			//nodes to right (including the insertion point) have index adjusted  (and parent pointers too).

			Schema::disableForeignKeyConstraints();
			$node->newQuery()->where('index', '>=', $node->index)->update(['index' => DB::raw("`index` $adj")]);
			$node->newQuery()->where('parent', '>=', $node->index)->update(['parent' => DB::raw("parent $adj")]);
			Schema::enableForeignKeyConstraints();
		}
	}
}
