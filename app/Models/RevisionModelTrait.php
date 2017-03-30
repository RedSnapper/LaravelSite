<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 30/03/2017 10:19
 */

namespace App\Models;

use App\Models\Helpers\TreeModelObserver;

trait RevisionModelTrait {
	protected static function boot() {
		if(method_exists(parent::class,'boot')) {
			parent::boot();
			self::observe(RevisionModelObserver::class);
		}
	}

}