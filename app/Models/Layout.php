<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
	protected $fillable = [
		'name','category_id'
	];

	public function mm() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}

	public function category() {
//	HasOne for 1:1 relation .
//	BelongsTo for 1:* relation
		return $this->belongsTo(Category::class);
	}

	public function segments() {
		return $this->belongsToMany(Segment::class)->withPivot('syntax');
	}

}
