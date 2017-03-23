<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
	protected $fillable = [
		'name','docs'
	];

	/**
	 * many-many relations
	 **/

	public function mm() {
		return $this->belongsToMany(Layout::class)->withPivot('syntax');
	}

}
