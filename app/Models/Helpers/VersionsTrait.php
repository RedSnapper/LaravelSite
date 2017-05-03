<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 30/03/2017 10:19
 */

namespace App\Models\Helpers;

use Illuminate\Database\Eloquent\Builder;


trait VersionsTrait {

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
		return;
	}
	public function redo(int $steps = 1) {
		return;
	}


}