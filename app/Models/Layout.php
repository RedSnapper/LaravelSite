<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
	protected $fillable = [
		'name'
	];

	public function mm() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}

	public function segments() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}
}
