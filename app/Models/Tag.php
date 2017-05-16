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

	public static function options(Collection $categories = null) {
		return with(new static)->whereIn('category_id',$categories)->pluck('name','id');
	}

}
