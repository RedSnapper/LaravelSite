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
		if (method_exists(parent::class, 'boot')) {
			parent::boot();
			self::observe(TreeObserver::class);
		}
	}

	public function moveBefore(TreeInterface $sibling,TreeInterface $parent) {
		return $this->update(['idx' => $sibling->idx,'parent' => $parent->idx]);
	}

	public function moveAfter(TreeInterface $sibling) {
		$siblingRight = $this->index($sibling->idx + $sibling->size)->first();
		if(is_null($siblingRight) || ($siblingRight->parent != $sibling->parent)) {
			return  $this->update(['idx' => null, 'parent'=> $sibling->parent ] );
		} else {
			return  $this->update(['idx' => ($sibling->idx + $sibling->size),'parent'=> $sibling->parent ] );
		}
	}

	public function moveInto(TreeInterface $parent) {
		return $this->update(['parent' => $parent->idx]);
	}

	private function indexWithModel(Builder $query, TreeInterface $index) {
		return $query->where('idx', $index->id);
	}

	public function scopeIndex(Builder $query, $index) {
		if(is_a($index,TreeInterface::class)) {
			return $this->indexWithModel($query,$index);
		}
		return $query->where('idx', $index);
	}

	public function scopeReference(Builder $query, string $reference, string $scope = "") {
		if (!empty($scope)) {
			$table = $this->getTable();
			return $query->select("$table.*")->join("$table as a", function ($join) use ($table) {
				return $join->on(DB::raw('(a.idx+a.size)'), '>', "$table.idx")->on("a.idx", '<', "$table.idx");
			})->where("$table.section", false)->where("$table.name", $reference)->where("a.section", true)->where("a.name", $scope);
		} else {
			return $query->where('name', $reference)->where('section', false);
		}
	}

	public function scopeSection(Builder $query, string $reference) {
		return $query->where('name', $reference)->where('section', true);
	}

	public function scopeSections(Builder $query) {
		return $query->where('idx','!=','1')->where('section', true);
	}

	public function scopeParent(Builder $query) {
		return $query->where('idx', $this->parent);
	}

	public function scopeAncestors(Builder $query, bool $self = false) {
		$plus = $self ? [$this->parent, $this->idx] : [$this->parent];
		return $query->where(function ($node) {
			$node->where(DB::raw('(idx+size)'), '>=', $this->idx)->where('idx', '<', $this->parent);
		})->orWhereIn('idx', $plus)->ordered();
	}

	public function scopeSiblings(Builder $query, bool $self = true) {
		if ($self) {
			return $query->where('parent', $this->parent)->ordered();
		} else {
			return $query->where('parent', $this->parent)->where('idx', '!=', $this->idx)->ordered();
		}
	}

	public function scopeChildren(Builder $query) {
		return $query->where('parent', '=', $this->idx)->ordered();
	}

	public function scopeDescendants(Builder $query, bool $self = false, bool $ordered = true) {
		$alsoSelf = $self ? '>=' : '>';
		$result = $query->where("idx", "<", ($this->idx + $this->size))->where("idx", $alsoSelf, $this->idx);
		if ($ordered) {
			$result = $result->ordered();
		}
		return $result;
	}

	public function scopeOrdered(Builder $query) {
		return $query->orderBy('idx', 'asc');
	}

	/**
	 * compose() uses the values from array data to recursively compose a valid branch.
	 * It needs a parent node under which the data will be added.
	 * example: 	$branch=['§ROLES'=>['General Roles','Team Roles'=>['Happy','Sad']],'§SEGMENTS'=>['General Purpose']];
	 * Category::compose($root,$branch); //$root has already been determined..
	 *
	 * Quite possibly this method should belong to the trait itself.
	 * @param TreeInterface $parent
	 * @param array $children
	 *
	 */
	public function compose(TreeInterface $parent,array $children) {
		foreach ($children as $childName => $kids) {
			if(is_int($childName)) { //it's a leaf node. '$kids' is it's name, thanks to the oddities of php.
				$childName = $kids;
				$kids = [];
			}
			$section = false;
			if(mb_substr($childName, 0, 1) == '§') { //Section Character.
				$section = true;
				$childName = mb_substr($childName,1);
			}
			$child = $this->create(['parent'=>$parent->idx,'name'=>$childName,'section'=>$section]);
			if(count($kids) > 0) {
				$this->compose($child,$kids);
			}
		}
	}


	public function checkIntegrity(): array {
		$result = [];
		$table = $this->getTable();

		//select *  from node where nextchild = 0;
		$bad_nodes = $this->newQuery()
			->where('size', '=', 0)->get();
		if ($bad_nodes->count() > 0) {
			$result['invalid branch sizes'] = $bad_nodes->all();
		}

		//select *  from node where nextchild > (select count(*)+1 from node);
		$bad_nodes = $this->newQuery()
			->where(DB::raw('(idx+size)'), '>', function ($query) use ($table) {
				$query->selectRaw('count(*) + 1')->from($table);
			})->get();
		if ($bad_nodes->count() > 0) {
			$result['invalid nextchild boundary nodes'] = $bad_nodes->all();
		}

		//select * from node n left join node m on m.idx = if((n.idx-1) = 0,n.nextchild-1,n.idx-1) where n.idx is null;
		$bad_nodes = DB::table("$table as n")->leftJoin("$table as m", function ($join) {
			$join->on('m.idx', '=', DB::Raw("if((n.idx-1) = 0,(n.idx+n.size)-1,n.idx-1)"));
		})->whereNull('n.idx')->get();
		if ($bad_nodes->count() > 0) {
			$result['invalid index ordinal'] = $bad_nodes->all();
		}

		//select * from node p,node n where p.id=n.p and not(p.idx < n.idx and p.nextchild > n.idx)";
		$bad_nodes = DB::table("$table as m")->join("$table as d", function ($join) {
			$join->on('d.idx', '<', DB::Raw("(m.idx+m.size) and d.idx > m.idx and not(d.parent >= m.idx or (d.idx+d.size) <= (m.idx+m.size) or d.parent < (m.idx+m.size))"));
		})->get();
		if ($bad_nodes->count() > 0) {
			$result['greedy descendants'] = $bad_nodes->all();
		}

		//select m.* from `node` as `m` left join `node` as `d` on `d`.`idx` < m.nextchild and d.idx > m.idx and d.parent >= m.idx and d.nextchild <= m.nextchild and d.parent < m.nextchild group by m.id having m.size!=count(d.id)+1
		$bad_nodes = DB::table("$table as m")->select(DB::Raw('m.*'))->leftJoin("$table as d", function ($join) {
			$join->on('d.idx', '<', DB::Raw("(m.idx+m.size) and d.idx > m.idx and d.parent >= m.idx and (d.idx+d.size) <= (m.idx+m.size) and d.parent < (m.idx+m.size)"));
		})->groupBy('m.id')->havingRaw("m.size!=count(d.id)+1")->get();
		if ($bad_nodes->count() > 0) {
			$result['descendants not well-formed'] = $bad_nodes->all();
		}

		$bad_nodes = collect(DB::select(DB::Raw("select m.*,1+count(a.id) as tier from $table as m,$table as a 
		where (a.idx+a.size) > m.idx and a.idx < m.idx group by m.id having m.depth != tier")));
		if ($bad_nodes->count() > 0) {
			$result['incorrect depths'] = $bad_nodes->all();
		}

		//select * from node where parent >= n.idx";
		$bad_nodes = $this->newQuery()->whereColumn('parent', '>=', 'idx')->get();
		if ($bad_nodes->count() > 0) {
			$result['badly ordered parent'] = $bad_nodes->all();
		}

		//select * from node p,node n where p.id=n.p and not(p.idx < n.idx and p.nextchild > n.idx)";
		$bad_nodes = DB::table("$table as p")->join("$table as n", function ($join) {
			$join->on('p.idx', '=', DB::Raw("n.parent and not(p.idx < n.idx and (p.idx+p.size) > n.idx)"));
		})->get();
		if ($bad_nodes->count() > 0) {
			$result['self-descendant parents'] = $bad_nodes->all();
		}

		//select * from node as n inner join `node` as m on (m.idx=n.nextchild and n.size != m.idx-n.idx)
		$bad_nodes = DB::table("$table as n")->join("$table as m", function ($join) {
			$join->on('m.idx', '=', DB::Raw("(n.idx+n.size) and n.size != m.idx-n.idx"));
		})->get();
		if ($bad_nodes->count() > 0) {
			$result['bad branch sizes'] = $bad_nodes->all();
		}

		return $result;
	}
}