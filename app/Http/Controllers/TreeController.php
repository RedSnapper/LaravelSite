<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 19/04/2017 12:27
 */

namespace App\Http\Controllers;


use App\Models\Helpers\TreeInterface;
use App\Models\Helpers\Node;

class TreeController {
	/**
	 * @var TreeInterface
	 */
	private $node;

	public function __construct(TreeInterface $node) {
		$this->node = $node;
	}


	public function branch($name = 'ROOT',\Closure $allow = null): array {
		$node = $this->node->section($name)->first();
		$items = $node->descendants(true)->get();
		$objects = [];
		$nodes = [];
		foreach ($items as $item) {
			$objects[$item->idx] = $item; //so we can get parent from object.
			$nodes[$item->idx] = new Node($item->id, $item->name);
			if ($item->name != $name) {
				if ($this->allowed($allow,$item)) {
					if ($this->allowed($allow,$objects[$item->parent])) {
						$nodes[$item->parent]->addChild($nodes[$item->idx]);
					} else {
						$nodes[$node->idx]->addChild($nodes[$item->idx]);
					}
				}
			}
		}
		return reset($nodes)->children;
	}

	public function options(string $reference,\Closure $allow = null) {
		$node = $this->node->section($reference)->first();
		$items = $node->descendants(false)->get();
		if(is_null($allow)) {
			return $items;
		} else {
			return $items->filter($allow);
		}
	}

	public function createNode(int $parentId = null,string $name,\Closure $allow = null): TreeInterface {
		$fields = ['name' => $name];
		if (!is_null($parentId)) {
			$parent = $this->node->find($parentId);
			if (!$this->allowed($allow,$parent)) {
				return null; //not allowed to do anything.
			}
			$fields['parent'] = $parent->idx;
		}
		return $this->node->create($fields); //we need a TreeModelInterface instead.
	}

	public function moveBefore(TreeInterface $node,int $sibling,\Closure $allow = null): bool {
		$sibling = $node->find($sibling);
		$parent = $sibling->parent()->first();
		if ($this->allowed($allow,$parent)) {
			return $node->moveBefore($sibling,$parent);
		}
		return false;
	}

	public function moveAfter(TreeInterface $node, int $sibling, \Closure $allow = null): bool {
		$sibling = $node->find($sibling);
		if ($this->allowed($allow,$sibling->parent()->first())) {
			return $node->moveAfter($sibling);
		}
		return false;
	}

	public function moveInto(TreeInterface $node, int $parentId, \Closure $allow = null): bool {
		$parent = $node->find($parentId);
		if ($this->allowed($allow,$parent)) {
			return $node->moveInto($parent);
		}
		return false;
	}

	protected function allowed(\Closure $closure=null,$node):bool{
		return is_null($closure) || $closure($node);
	}

}