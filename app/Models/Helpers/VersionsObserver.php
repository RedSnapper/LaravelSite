<?php
/**
 * Part of form
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 30/03/2017 10:20
 */

namespace App\Models\Helpers;

use Illuminate\Support\Facades\DB;

class VersionsObserver {
	/**
	 *
	 * @param VersionsInterface $model
	 * @return bool
	 */
	public function updating(VersionsInterface $model) {
		$org = $model->getOriginal(); 		 //Get the original values of the node.
		$table = $model->versionsTable();
		$org['master_id'] = $org['id']; unset($org['id']);
		/**
		 * So now imagine ... (after an undo() from 25)

		 * prime
		+----+---------+---------+
		| id | prev_id | next_id |
		+----+---------+---------+
		|  8 |    NULL |      25 |
		+----+---------+---------+

		 * versions
		+----+---------+---------+
		| id | prev_id | next_id |
		+----+---------+---------+
		| 24 |    NULL |      25 |
		| 25 |      24 |    NULL |
		+----+---------+---------+

		 * we do an insert/getID into versions..
		+----+---------+---------+
		| id | prev_id | next_id |
		+----+---------+---------+
		| 24 |    NULL |      25 | old
		| 25 |      24 |    NULL | head
		| 26 |    NULL |    NULL | newer..
		+----+---------+---------+
		 * that's not good...
		 *
		 * ok so how about this?
		+----+---------+---------+
		| id | prev_id | next_id |
		+----+---------+---------+
		| 24 |    NULL |      25 | old
		| 25 |      24 |     *26 | head
		| 26 |      25 |    NULL | newer..
		+----+---------+---------+

		 */
		if(!is_null($org['next_id']) && is_null($org['prev_id'])) {
			$org['prev_id'] = $org['next_id'];
		}

		$id = DB::table($table)->insertGetId($org);
		if(!is_null($org['prev_id'])) {
			DB::table($table)->where('id',$org['prev_id'])->update(['next_id' => $id]);
		}
		$model->prev_id = $id;
		$model->next_id = null;
		return true;
	}
}