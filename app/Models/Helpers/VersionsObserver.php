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
		/**
		 * We need to save the version that was.
		 * This is 'org', from the getOriginal method.
		 * We don't need to save it, IF it was a version already.
		 *
		 */
		$org = $model->getOriginal(); 		 //Get the original values of the node.
		$table = $model->versionsTable();

		if (is_null($org['version'])) {
			$org['primary'] = $org['id'];
			unset($org['id']);
			unset($org['version']);

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
		} else {
			$model->version = null;
			$model->prev_id = $org['version'];
		}
		$model->next_id = null;
		return true;
	}
}