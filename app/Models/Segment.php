<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
	protected $fillable = [
		'name','docs','syntax','size','category_id'
	];

	//protected $casts = [
	//	'size' => 'array'
	//];

	public function category() {
		return $this->hasOne(Category::class);
	}

	/**
	 * many-many relations
**/

	public function layouts() {
		return $this->belongsToMany(Layout::class)->withPivot('syntax');
	}

}
