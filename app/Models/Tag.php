<?php

namespace App\Models;

use App\Http\Controllers\TreeController;
use App\Models\Helpers\Node;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class Tag extends Model
{
	//!! cast of null doesn't seem to cast to integer..
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
	 * For some reason unknown to me, the 'cast' doesn't seem to work on null.
	 */
	public function setModeratedAttribute($value) {
		$this->attributes['moderated'] = $value ? 1 : 0;
	}

	public static function options(Collection $categories = null) {
		return with(new static)->whereIn('category_id',$categories)->pluck('name','id');
	}

	public static function optGroup(Category $base) {
		$branch = (new TreeController(Category::root()))->nodeBranch($base);
		$bKeys = $branch->keys([],false); //don't want the root node included.
		$tags = with(new static)->whereIn('category_id',$bKeys)->get();
		$result = $branch->merge($tags,'category_id')->asOptGroup(false);
		return $result;
	}

}
