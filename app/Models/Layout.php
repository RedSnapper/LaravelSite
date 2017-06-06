<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layout extends Model
{
	protected $casts = [
		'build_point' => 'integer',
		'searchable' => 'integer',
	];

	protected $fillable = [
		'name','category_id','build_point','searchable','default_child','icon'
	];

	public function category() {
//	HasOne for 1:1 relation .
//	BelongsTo for 1:* relation
		return $this->belongsTo(Category::class);
	}

	public function setSearchableAttribute($value) {
		$this->attributes['searchable'] = $value ? 1 : 0;
	}

	public function setBuildPointAttribute($value) {
		$this->attributes['build_point'] = $value ? 1 : 0;
	}

	public function segments() {
		return $this->belongsToMany(Segment::class)->withPivot(['syntax','local_name','tab']);
	}

	public function addSegment(Segment $segment){
		return $this->segments()->save($segment);
	}

	public static function options() {
			return with(new static)->pluck('name','id');
	}


}
