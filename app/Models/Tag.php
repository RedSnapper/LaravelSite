<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class Tag extends Model
{
	protected $fillable = [
		'name','category_id','moderated'
	];

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function media() {
		return $this->belongsToMany(Media::class);
	}

	/**
	 * This comes (most likely) from a form-post checkbox.
	 * @param $value
	 * @return int
	 */
	public function setModeratedAttribute($value) : int {
		return is_null($value) ? 0 : 1 ;
	}

	public static function options(Collection $categories = null) {
		return with(new static)->whereIn('category_id',$categories)->pluck('name','id');
	}

}
