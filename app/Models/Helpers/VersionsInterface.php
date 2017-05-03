<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 03/05/2017 12:13
 */

namespace App\Models\Helpers;

use Illuminate\Database\Eloquent\Builder;

interface VersionsInterface {

	//methods that must be set by the model.
		public function versionsTable() : string;

//methods set by the trait
	public function scopeHistory(Builder $query);
	public function undo(int $steps = 1);
	public function redo(int $steps = 1);

}