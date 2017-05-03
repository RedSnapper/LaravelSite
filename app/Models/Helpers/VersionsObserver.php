<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 30/03/2017 10:20
 */

namespace App\Models\Helpers;

use Illuminate\Support\Facades\DB;

class VersionsObserver {
	public function updating(VersionsInterface $model) {
		$org = $model->getOriginal(); 		 //Get the original values of the node.
		$table = $model->versionsTable();
		$org['master_id'] = $org['id']; unset($org['id']);
		$id = DB::table($table)->insertGetId($org);
		$model->version_id = $id;
		return true;
	}
}