<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 29/03/2017 09:48
 */

namespace App\Models\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait TreeTrait {

	protected static function boot() {
		if(method_exists(parent::class,'boot')) {
			parent::boot();
			self::observe(TreeObserver::class);
		}
	}

	public function branch($name = 'ROOT'): array {
		$node = $this->section($name)->first();
		$items = $node->descendants(true)->get();
		$objects = [];
		$nodes = [];
		foreach ($items as $item) {
			$objects[$item->idx] = $item; //so we can get parent from object.
			$nodes[$item->idx] = new Node($item->id, $item->name);
			if ($item->name != $name) {
				if($this->canView($item)) {
					if ($this->canView($objects[$item->parent])) {
						$nodes[$item->parent]->addChild($nodes[$item->idx]);
					} else {
						$nodes[$node->idx]->addChild($nodes[$item->idx]);
					}
				}
			}
		}
		return reset($nodes)->children;
	}

	public static function options(string $reference) {
		$node  = with(new static)->section($reference)->first();
		return $node->descendants(false)->pluck('name','id');
	}

	public function createNode(int $parentId = null, string $name) : TreeInterface {
		$fields = ['name'=> $name];
		if(!is_null($parentId)) {
			$fields['parent'] = $this->find($parentId)->idx;
		}
		return  $this->create($fields);
	}

	//public function moveTo(int $parentId = null,int $indexReplace = null) {
	//	$fields = [];
	//	if(!is_null($indexReplace)) {
	//		$fields['idx'] = $this->find($indexReplace)->idx;
	//	} elseif (is_null($parentId)) {
	//		return;
	//	}
	//	if(!is_null($parentId)) {
	//		$fields['parent'] = $this->find($parentId)->idx;
	//	}
	//	return  $this->update($fields);
	//}

	public function moveAfter(int $sibling):bool {
		$siblingLeft = $this->find($sibling);
		$siblingRight = $this->index($siblingLeft->nextchild)->first();
		$parent = $siblingLeft->parent()->first();
		if($this->canUpdate($parent)) {
			if(is_null($siblingRight) || ($siblingRight->parent != $siblingLeft->parent)) {
				return  $this->update(['idx' => null, 'parent'=> $siblingLeft->parent ] );
			} else {
				return  $this->update(['idx' => $siblingLeft->nextchild,'parent'=> $siblingLeft->parent  ] );
			}
		}
		return false;
	}

	public function moveBefore(int $sibling):bool {
		$sibling = $this->find($sibling);
		$parent = $sibling->parent()->first();
		if($this->canUpdate($parent)) {
			return  $this->update(['idx' => $sibling->idx,'parent' => $parent->idx]);
		}
		return false;
	}

	public function moveInto(int $parentId) {
		$parent = $this->find($parentId);
		if($this->canUpdate($parent)) {
			return $this->update(['parent' => $parent->idx]);
		} else {
			return false;
		}
	}

	protected function canUpdate(TreeInterface $node){
		return true;
	}
	protected function canView(TreeInterface $node){
		return true;
	}


	public function scopeIndex(Builder $query,int $index){
		return $query->where('idx', $index);
	}

	public function scopeReference(Builder $query,string $reference,string $scope = "") {
		if(!empty($scope)) {
			$table = $this->getTable();
			return $query->select("$table.*")->join("$table as a",function ($join) use ($table) {
				return $join->on("a.nextchild",'>',"$table.idx")->on("a.idx",'<',"$table.idx");
			})->where("$table.section",false)->where("$table.name",$reference)->where("a.section",true)->where("a.name",$scope);
		} else {
			return $query->where('name',$reference)->where('section',false);
		}
	}

	public function scopeSection(Builder $query,string $reference){
		return $query->where('name',$reference)->where('section',true);
	}

	public function scopeParent(Builder $query){
		return $query->where('idx', $this->parent);
	}

	public function scopeAncestors(Builder $query,bool $self = false){
		$plus = $self ? [$this->parent,$this->idx] : [$this->parent];
		return $query->where(function($node) {$node->where('nextchild', '>=', $this->idx)->where('idx','<',$this->parent);})->orWhereIn('idx',$plus)->ordered();
	}
	public function scopeSiblings(Builder $query,bool $self = true){
		if($self) {
			return $query->where('parent', $this->parent)->ordered();
		} else {
			return $query->where('parent', $this->parent)->where('idx', '!=',$this->idx)->ordered();
		}
	}

	public function scopeChildren(Builder $query){
		return $query->where('parent', '=', $this->idx)->ordered();
	}
	public function scopeDescendants(Builder $query,bool $self = false,bool $ordered = true){
		$alsoSelf = $self ? '>=' : '>';
		$result = $query->where("idx","<", $this->nextchild)->where("idx", $alsoSelf, $this->idx);
		if($ordered) {
			$result = $result->ordered();
		}
		return $result;
	}

	public function scopeOrdered(Builder $query) {
		return $query->orderBy('idx','asc');
	}
	public function checkIntegrity() : array {
		$result = [];
		$table = $this->getTable();

		//select *  from node where nextchild = 0;
		$bad_nodes = $this->newQuery()
			->where('size','=',0)->get();
		if($bad_nodes->count() > 0) {$result['invalid branch sizes'] = $bad_nodes->all(); }

		//select *  from node where nextchild > (select count(*)+1 from node);
		$bad_nodes = $this->newQuery()
			->where('nextchild','>',function($query) use($table) {
				$query->selectRaw('count(*) + 1')->from($table);
			})->get();
		if($bad_nodes->count() > 0) {$result['invalid nextchild boundary nodes'] = $bad_nodes->all(); }

		//select * from node n left join node m on m.idx = if((n.idx-1) = 0,n.nextchild-1,n.idx-1) where n.idx is null;
		$bad_nodes =  DB::table("$table as n")->leftJoin("$table as m",function ($join) {
			$join->on('m.idx', '=', DB::Raw("if((n.idx-1) = 0,n.nextchild-1,n.idx-1)"));
		})->whereNull('n.idx')->get();
		if($bad_nodes->count() > 0) {$result['invalid index ordinal'] = $bad_nodes->all(); }

		//select * from node p,node n where p.id=n.p and not(p.idx < n.idx and p.nextchild > n.idx)";
		$bad_nodes =  DB::table("$table as m")->join("$table as d",function ($join) {
			$join->on('d.idx','<',DB::Raw("m.nextchild and d.idx > m.idx and not(d.parent >= m.idx or d.nextchild <= m.nextchild or d.parent < m.nextchild)"));
		})->get();
		if($bad_nodes->count() > 0) {$result['greedy descendants'] = $bad_nodes->all(); }

		//select m.* from `node` as `m` left join `node` as `d` on `d`.`idx` < m.nextchild and d.idx > m.idx and d.parent >= m.idx and d.nextchild <= m.nextchild and d.parent < m.nextchild group by m.id having m.size!=count(d.id)+1
		$bad_nodes =  DB::table("$table as m")->select(DB::Raw('m.*'))->leftJoin("$table as d",function ($join) {
			$join->on('d.idx', '<', DB::Raw("m.nextchild and d.idx > m.idx and d.parent >= m.idx and d.nextchild <= m.nextchild and d.parent < m.nextchild"));
		})->groupBy('m.id')->havingRaw("m.size!=count(d.id)+1")->get();
		if($bad_nodes->count() > 0) {$result['descendants not well-formed'] = $bad_nodes->all(); }

		$bad_nodes =  collect(DB::select(DB::Raw("select m.*,1+count(a.id) as tier from $table as m,$table as a 
		where a.nextchild > m.idx and a.idx < m.idx group by m.id having m.depth != tier")));
		if($bad_nodes->count() > 0) {$result['incorrect depths'] = $bad_nodes->all(); }

		//select * from node where parent >= n.idx";
		$bad_nodes = $this->newQuery()->whereColumn('parent','>=','idx')->get();
		if($bad_nodes->count() > 0) {$result['badly ordered parent'] = $bad_nodes->all(); }

		//select * from node p,node n where p.id=n.p and not(p.idx < n.idx and p.nextchild > n.idx)";
		$bad_nodes =  DB::table("$table as p")->join("$table as n",function ($join) {
			$join->on('p.idx', '=',DB::Raw("n.parent and not(p.idx < n.idx and p.nextchild > n.idx)"));
		})->get();
		if($bad_nodes->count() > 0) {$result['self-descendant parents'] = $bad_nodes->all(); }

		//select * from node as n inner join `node` as m on (m.idx=n.nextchild and n.size != m.idx-n.idx)
		$bad_nodes =  DB::table("$table as n")->join("$table as m",function ($join) {
			$join->on('m.idx', '=',DB::Raw("n.nextchild and n.size != m.idx-n.idx"));
		})->get();
		if($bad_nodes->count() > 0) {$result['bad branch sizes'] = $bad_nodes->all(); }

		return $result;
	}

	private static function allowed(\Closure $closure,TreeInterface $node) : bool {
		return is_null($closure) || $closure($node);
	}

}