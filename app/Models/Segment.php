<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model {
	protected $fillable = [
	  'name',
	  'docs',
	  'syntax',
	  'category_id'
	];

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function layouts() {
		return $this->belongsToMany(Layout::class)->withPivot('syntax');
	}

}
