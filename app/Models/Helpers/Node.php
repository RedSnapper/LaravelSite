<?php
namespace App\Models\Helpers;
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
	public function addChild(Node $child) {
		$this->children[] = $child;
	}
	public function __toString() {
		return json_encode($this,0,1024);
	}
}
