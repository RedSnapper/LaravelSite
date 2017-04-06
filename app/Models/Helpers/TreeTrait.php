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


	public static function nodeBranch($name='ROOT') : array {
		$node  = with(new static)->section($name)->first();
		$items = $node->descendants(true)->get();
		$nodes = [];
		foreach($items as $item) {
			$nodes[$item->idx] = new Node($item->id,$item->name);
			if($item->name != $name) {
				$nodes[$item->parent]->addChild($nodes[$item->idx]);
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

	public function moveTo(int $parentId = null,int $indexReplace = null) {
		$fields = [];
		if(!is_null($indexReplace)) {
			$fields['idx'] = $this->find($indexReplace)->idx;
		} elseif (is_null($parentId)) {
			return;
		}
		if(!is_null($parentId)) {
			$fields['parent'] = $this->find($parentId)->idx;
		}
		return  $this->update($fields);
	}

	public function moveAfter(int $sibling) {
		$siblingLeft = $this->find($sibling);
		$siblingRight = $this->index($siblingLeft->nextchild)->first();
		if(is_null($siblingRight) || ($siblingRight->parent != $siblingLeft->parent)) {
			return  $this->update(['idx' => null, 'parent'=> $siblingLeft->parent ] );
		} else {
			return  $this->update(['idx' => $siblingLeft->nextchild,'parent'=> $siblingLeft->parent  ] );
		}
	}

	public function moveBefore(int $sibling) {
		return  $this->update(['idx' => $this->find($sibling)->idx ]);
	}

	public function moveInto(int $parentId) {
		return  $this->update(['parent' => $this->find($parentId)->idx ]);
	}

	public function scopeIndex(Builder $query,int $index){
		return $query->where('idx', $index);
	}

	public function scopeReference(Builder $query,string $reference){
		return $query->where('name',$reference)->where('section',false);
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

	public function scopeTier(Builder $query,$columns = ['aggregate']){
		return $query->where('nextchild', '>', $this->idx)->where('idx', '<',$this->idx)->count('*',$columns);
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

}