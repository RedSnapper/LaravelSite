<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model {

	protected $fillable = [
		'name','label','category_id','comment'
	];

	public function roles() : BelongsToMany {
		$result = $this->belongsToMany(Role::class);
//		$result->getQuery()->where("roles.team_based",false); //commented because it may be plausible for material roles to be available to teams.
		return $result;
	}

	public function category() {
		return $this->belongsTo(Category::class);
	}

}
