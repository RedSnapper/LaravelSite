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
		$org = $node->getOriginal();
		$node->sz = $org['sz'];
		if($node->tw != $org['tw'] || $node->pa != $org['pa'] ) {
			$orig = new get_class($node); //This will be the mirror of the original.
			$orig->setRawAttributes($org);
			$orgTw = $orig->tw;
			$treeSize = $node->count();
			$nodeSize = $node->sz;
			$earthSize = $treeSize - $nodeSize;
			$heaven = $treeSize + 1000;
			if ($node->tw == $orgTw) { //we only have the parent.
				$ref = $node->newQuery()->index($node->pa,['nc']); //now we have the parent's nc.
				$node->tw = $ref->nc;
			} else { //moving by tw. if tw is same then we need to see if it's pa
				$ref = $node->newQuery()->index($node->tw,['pa']);
				$node->pa = $ref->pa;
			}
			//now both the tw and pa are ready. Let's check that they have actually changed after all.
			if ($node->tw != $orgTw || $node->pa != $orig->pa) {   //drop through to new position
				$node->tw = $node->tw > $orgTw ? $node->tw - $nodeSize : $node->tw; //while in heaven, we will change the size of earth.
				$node->pa = $node->pa > $orgTw ? $node->pa - $nodeSize : $node->pa; //so our own reference points must be adjusted.
				if($node->tw <= ($earthSize+1) && $node->pa <= $earthSize) { //when going right to the end of the tree, earthsize +1 is legal.
					//need to move the branch into the stratosphere.
					$twOffset = $heaven + ($node->tw - $orgTw); //how many places we are moving. ie remove old tw and add new tw.
					$twAdj = $twOffset > 0 ? "+$twOffset" : "$twOffset";
					$paAdj = $heaven + $node->pa;
					//send branch to heaven.
					Schema::disableForeignKeyConstraints();
					//The branch root's parent is different = it's the node->pa + heaven.
					$orig->newQuery()->descendants(true)->update(['tw' => DB::raw("tw $twAdj"),'pa' => DB::raw("if(pa < $orgTw,$paAdj,pa $twAdj)")]);
					Schema::enableForeignKeyConstraints();

					$this->adjustTree($orig,false); //downsize the tree (the branch has gone to heaven).
					$this->adjustTree($node,true);  //resize the tree to make space for the returning branch

					//return branch back to earth.
					Schema::disableForeignKeyConstraints();
					$node->newQuery()
						->where('tw', '>=', $heaven + $node->tw)
						->where('tw', '<', $heaven + $node->tw + $nodeSize)
						->update(['tw' => DB::raw("tw - $heaven"),'pa' => DB::raw("pa - $heaven")]);
					Schema::enableForeignKeyConstraints();
				} else {
					$node->tw = $orgTw;
					$node->pa = $orig->pa;
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
		 * (2) a treewalk. It will inherit the current tw position and nodes to it's right will be shifted.
		 */
		$node->sz = 1; //The size of a new node is always going to be 1. so the nc will be tw+1;
		$root = $node->index(1,['tw','nc']);
		if(isset($root->tw)) {
			if(!isset($node->tw) || ($node->tw > $root->nc) || ($node->tw < 1)) {
				if(!isset($node->pa)) {
					$parent = $root;
				} else {
					$parent = $node->index($node->pa,['tw','nc']);
				}
				$node->pa = $parent->tw;
				$node->tw = $parent->nc;
			} else {
				//new tree.
				$curr = $node->index($node->tw,['pa','tw']);
				$node->pa = $curr->pa;
				$node->tw = $curr->tw;
			}
			$this->adjustTree($node,true); //inserting
		} else {
			$node->tw = 1;
			$node->pa = null;
		}
		return true;
	}

	/**
	 * Adjust the tree for the insertion of a node or branch.
	 * How to work out what needs changing..
	 * (1) all the tw >= the new tw need to be incremented (this is our ordinal sequence).
	 * (2) the size of all ancestors is incremented. (this is a virtually done, as it's wrapped into the next point).
	 * (3) the revised size+tw of those nodes which have changed needs to be updated.
	 * I.e. nodes to the right have their nc and tw incremented while ancestors have their nc incremented.
	 * We have to be careful here because the right-branch of our immediate sibling to the left has the same nc as our
	 *  parent - and we don't want that changing.
	 *
	 * @param Category $node - must have pa/tw set correctly. This is where the new node/branch will be placed.
	 * @param int  $size - the size of the branch that is being inserted.
	 */
	private function adjustTree(TreeInterface $node,bool $inserting) {
		if(isset($node->tw) && isset($node->sz)) {
			$adj = ($inserting ? "+ " : "- ") .$node->sz; //insertion or deletion
			//ancestors have sz adjusted. We need to force parent here because it's current nc is us and we don't want our left siblings.
			$node->newQuery()->ancestors(false)->update(['sz' => DB::raw("sz $adj")]);
			//nodes to right (including the insertion point) have tw adjusted  (and pa pointers too).

			Schema::disableForeignKeyConstraints();
			$node->newQuery()->where('tw', '>=', $node->tw)->update(['tw' => DB::raw("tw $adj")]);
			$node->newQuery()->where('pa', '>=', $node->tw)->update(['pa' => DB::raw("pa $adj")]);
			Schema::enableForeignKeyConstraints();
		}
	}
}
