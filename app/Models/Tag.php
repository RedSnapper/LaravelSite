<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class Tag extends Model
{
	protected $casts = [
		'moderated' => 'integer',
	];

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
	 * @return void
	 * formlet returns null for unchecked.
	 */
	//public function setModeratedAttribute($value) {
	//	$this->attributes['moderated'] = $value ? 1 : 0;
	//}

	public static function options(Collection $categories = null) {
		return with(new static)->whereIn('category_id',$categories)->pluck('name','id');
	}

}
