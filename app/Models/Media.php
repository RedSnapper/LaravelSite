<?php

namespace App\Models;

use App\Models\Helpers\VersionsInterface;
use App\Models\Helpers\VersionsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Laravel\Scout\Searchable;

class Media extends Model implements VersionsInterface {

	use VersionsTrait;
	use Searchable;


	protected $with = ['team','category'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'filename', 'category_id', 'team_id'];
	public function versionsTable() : string {
		return "media_versions";
	}

	public function saveMedia(array $fields, UploadedFile $file = null): Media {

		$this->fill($fields);

		if ($file) {
			$path = $file->store('/media');
			$this->path = $path;
			$this->mime = $file->getMimeType();
			$this->size = $file->getSize();
			$this->filename = $file->getClientOriginalName();
		}
		$this->save();

		return $this;
	}

	/**
	 * Get the thumbnail path
	 *
	 * @return string
	 */
	public function getThumbnailAttribute() {
		return route("img.thumbnail", $this->getMediaParameters());
	}

	/**
	 * Get the image path
	 *
	 * @return string
	 */
	public function getImagePathAttribute() {
		return route("img.show", $this->getMediaParameters());
	}

	public function category() {
		return $this->belongsTo(Category::class);
	}

	public function team() {
		return $this->belongsTo(Team::class);
	}

	public function scopeTeam($query,$team) {
		return $query->where('team_id',$team);
	}

	public function scopeCategory($query,$category) {
		return $query->where('category_id',$category);
	}

	/**
	 * Get parameters for media
	 *
	 * @return array
	 */
	protected function getMediaParameters(): array {
		return [
		  'id' => $this->id,
		  'ts' => $this->updated_at->timestamp
		];
	}

	public function toSearchableArray() {
		return [
		  'name'       => $this->name,
		  'filename'   => $this->filename,
		  'category'   => $this->category->name,
		  'created_at' => $this->created_at->toDateTimeString(),
		  'updated_at' => $this->updated_at->toDateTimeString()
		];
	}

}
