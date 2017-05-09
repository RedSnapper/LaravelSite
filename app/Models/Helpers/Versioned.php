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

//	protected $guarded = ['id', 'prev_id', 'next_id', 'head'];

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
		$attr = $this->getAttributes();
		$prev = $attr['prev_id'];
		if(is_null($prev)) return;
		if(!($attr['version'])) {
			// If there is no current version, we need to store the current version as the last version.
			// However we may have already done that when we last did an undo. (eg we've done an undo-redo, and now are doing an undo again).
			$attr['primary'] = $attr['id'];
			unset($attr['id']); unset($attr['version']);
			$id = DB::table($versions)->insertGetId($attr);
			DB::table($versions)->where('id',$prev)->update(['next_id' => $id]);
		}
		$previous = (array) DB::table($versions)->find($this->prev_id); // find returns a stdClass (because table is not a model).
		$previous['version'] = $previous['id'];
		unset($previous['id']);
		unset($previous['primary']);
		DB::table($maindata)->where('id',$this->id)->update($previous);
		return;
	}
	public function redo(int $steps = 1) {
		$versions = $this->versionsTable();
		$maindata = $this->getTable();
		$attr = $this->getAttributes();
		$next = $attr['next_id'];
		if(is_null($next)) return;
		$next = (array) DB::table($versions)->find($this->next_id); // find returns a stdClass (because table is not a model).
		$next['version'] = $next['id'];
		unset($next['id']);
		unset($next['primary']);
		DB::table($maindata)->where('id',$this->id)->update($next);
		return;
	}


}