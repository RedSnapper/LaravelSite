<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 30/03/2017 10:19
 */

namespace App\Models\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait Versioned {

//	protected $guarded = ['id', 'prev_id', 'next_id'];

	protected static function boot() {
		if(method_exists(parent::class,'boot')) {
			parent::boot();
			self::observe(VersionsObserver::class);
		}
	}

	public function scopeHistory(Builder $query) {
		return;
	}
	public function undo(int $steps = 1) {
		$versions = $this->versionsTable();
		$maindata = $this->getTable();
		if(is_null($this->prev_id)) {
			return;
		}
		$attr = $this->getAttributes();
		$prev = $attr['prev_id'];
		if(is_null($prev)) return;
		if(is_null($attr['next_id'])) { //we need to store the current version as the last version...
			$attr['master_id'] = $attr['id']; unset($attr['id']);
			$id = DB::table($versions)->insertGetId($attr);
			DB::table($versions)->where('id',$prev)->update(['next_id' => $id]);
		}
		$previous = (array) DB::table($versions)->find($this->prev_id); // find returns a stdClass (because table is not a model).
//		$previous['prev_id'] = $previous['prev_id'] ?? $previous['id'];
		unset($previous['id']);
		unset($previous['master_id']);
		DB::table($maindata)->where('id',$this->id)->update($previous);
		return;
	}
	public function redo(int $steps = 1) {
		return;
	}


}