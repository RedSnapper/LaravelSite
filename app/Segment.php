<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
	protected $fillable = [
		'name','docs','syntax'
	];

	/**
	 * many-many relations
	 **/

	public function layouts() {
		return $this->belongsToMany(Layout::class)->withPivot('syntax');
	}

}
