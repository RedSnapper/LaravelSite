<?php
namespace App\Models\Helpers;
use Illuminate\Support\Collection;

/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 30/03/2017 11:38
 */
class Node {
	public $id;
	public $name;
	public $children = [];
	public function __construct($id,$name) {
		$this->id = $id;
		$this->name = $name;
	}
	public function addChild(Node $child,$index=true) {
		if($index) {
			$this->children[$child->id] = $child;
		} else {
			$this->children[] = $child;
		}
	}
	public function __toString() {
		return json_encode($this,0,1024);
	}
	public function keys($result = [],$leaf = true) : array {
		if($leaf) {
			$result[] = $this->id;
		}
		foreach($this->children as $child) {
			$result = $child->keys($result);
		}
		return $result;
	}

	//$thing needs id,name properties..
	public function add($id,Node $thing) {
		if($this->id == $id) {
			$this->children[$thing->id] = $thing;
			return;
		}
		if(key_exists($id,$this->children)) {
			$this->children[$id]->children[$thing->id] = $thing;
			return;
		}
		foreach($this->children as $ident => $item) {
			$item->add($id,$thing);
		}
	}

	public function merge(Collection $items,$idName) : Node {
		foreach($items as $item) {
			$this->add($item->$idName,new Node($item->id,$item->name));
		}
		return $this;
	}

	public function asOptGroup() : array {
		if(count($this->children) > 0) {
			$children = [];
			foreach($this->children as $child) {
				if(count($child->children) > 0) {
					$children[$child->name]=$child->asOptGroup();
				} else {
					$children[$child->id]=$child->name;
				}
			}
			return $children;
		}
		return [];
	}

}
