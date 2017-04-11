<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Laravel\Scout\Searchable;

class Media extends Model {

	use Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'filename','category_id'];

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

	public function getThumbnailPath() {
		return route("img.thumbnail", $this->getMediaParameters());
	}

	public function getPath() {
		return route("img.show", $this->getMediaParameters());
	}

	public function category(){
		return $this->belongsTo(Category::class);
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
		  'name'     => $this->name,
		  'filename' => $this->filename,
		  'category' => $this->category->name,
		  'created_at'=> $this->created_at->toDateTimeString(),
		  'updated_at'=> $this->updated_at->toDateTimeString()
		];
	}

}
