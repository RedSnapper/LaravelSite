<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 29/03/2017 09:48
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AbstractTree extends Model {
	protected $guarded  = ['id','sz','nc'];
	public $timestamps = false;

	//		$node = Node::index(20); //C2.

	public function scopeAncestors(Builder $query,bool $self = false){
		$plus = $self ? [$this->pa,$this->tw] : [$this->pa];
		return $query->where(function($node) {$node->where('nc', '>=', $this->tw)->where('tw','<',$this->pa);})->orWhereIn('tw',$plus)->ordered();
	}
	public function scopeSiblings(Builder $query,bool $self = true){
		if($self) {
			return $query->where('pa', '=', $this->pa)->ordered();
		} else {
			return $query->where('pa', '=', $this->pa)->where('tw', '!=',$this->tw)->ordered();
		}
	}
	/*
	 * id is exactly the same as find().
	 * */
	public function scopeID(Builder $query,int $index, $columns = ['*']){
		return $query->where('id', '=', $index)->first($columns);
	}
	public function scopeIndex(Builder $query,int $index, $columns = ['*']){
		return $query->where('tw', '=', $index)->first($columns);
	}
	public function scopeReference(Builder $query,string $name, $columns = ['*']){
		return $query->where('name', '=', $name)->first($columns);
	}
	public function scopeParent(Builder $query, $columns = ['*']){
		return $query->where('pa', '=', $this->tw)->first($columns);
	}
	public function scopeChildren(Builder $query){
		return $query->where('pa', '=', $this->tw)->ordered();
	}
	public function scopeDescendants(Builder $query,bool $self = false){
		$alsoSelf = $self ? '>=' : '>';
		return $query->where("tw","<", $this->nc)->where("tw", $alsoSelf, $this->tw)->ordered();
	}
	public function scopeTier(Builder $query,$columns = ['aggregate']){
		return $query->where('nc', '>', $this->tw)->where('tw', '<',$this->tw)->count('*',$columns);
	}
	public function scopeOrdered(Builder $query) {
		return $query->orderBy('tw','asc');
	}
	public function checkIntegrity() : array {
		$result = [];
		$table = $this->getTable();

		//select *  from node where nc = 0;
		$bad_nodes = $this->newQuery()
			->where('sz','=',0)->get();
		if($bad_nodes->count() > 0) {$result['invalid branch sizes'] = $bad_nodes->all(); }

		//select *  from node where nc > (select count(*)+1 from node);
		$bad_nodes = $this->newQuery()
			->where('nc','>',function($query) use($table) {
				$query->selectRaw('count(*) + 1')->from($table);
			})->get();
		if($bad_nodes->count() > 0) {$result['invalid nc boundary nodes'] = $bad_nodes->all(); }

		//select * from node n left join node m on m.tw = if((n.tw-1) = 0,n.nc-1,n.tw-1) where n.tw is null;
		$bad_nodes =  DB::table("$table as n")->leftJoin("$table as m",function ($join) {
			$join->on('m.tw', '=', DB::Raw("if((n.tw-1) = 0,n.nc-1,n.tw-1)"));
		})->whereNull('n.tw')->get();
		if($bad_nodes->count() > 0) {$result['invalid tw ordinal'] = $bad_nodes->all(); }

		//select * from node p,node n where p.id=n.p and not(p.tw < n.tw and p.nc > n.tw)";
		$bad_nodes =  DB::table("$table as m")->join("$table as d",function ($join) {
			$join->on('d.tw','<',DB::Raw("m.nc and d.tw > m.tw and not(d.pa >= m.tw or d.nc <= m.nc or d.pa < m.nc)"));
		})->get();
		if($bad_nodes->count() > 0) {$result['greedy descendants'] = $bad_nodes->all(); }

		//select m.* from `node` as `m` left join `node` as `d` on `d`.`tw` < m.nc and d.tw > m.tw and d.pa >= m.tw and d.nc <= m.nc and d.pa < m.nc group by m.id having m.sz=count(d.id)+1
		$bad_nodes =  DB::table("$table as m")->select(DB::Raw('m.*'))->leftJoin("$table as d",function ($join) {
			$join->on('d.tw', '<', DB::Raw("m.nc and d.tw > m.tw and d.pa >= m.tw and d.nc <= m.nc and d.pa < m.nc"));
		})->groupBy('m.id')->having(DB::Raw("m.sz=count(d.id)+1"))->get();
		if($bad_nodes->count() > 0) {$result['descendants not well-formed'] = $bad_nodes->all(); }

		//select * from node p,node n where p.id=n.p and not(p.tw < n.tw and p.nc > n.tw)";
		$bad_nodes =  DB::table("$table as p")->join("$table as n",function ($join) {
			$join->on('p.tw', '=',DB::Raw("n.pa and not(p.tw < n.tw and p.nc > n.tw)"));
		})->get();
		if($bad_nodes->count() > 0) {$result['self-descendant parents'] = $bad_nodes->all(); }

		//select * from node as n inner join `node` as m on (m.tw=n.nc and n.sz != m.tw-n.tw)
		$bad_nodes =  DB::table("$table as n")->join("$table as m",function ($join) {
			$join->on('m.tw', '=',DB::Raw("n.nc and n.sz != m.tw-n.tw"));
		})->get();
		if($bad_nodes->count() > 0) {$result['bad branch sizes'] = $bad_nodes->all(); }

		return $result;
	}

}