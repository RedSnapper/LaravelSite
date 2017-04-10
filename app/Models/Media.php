<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Media extends Model {
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'filename'];

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

	/**
	 * Get parameters for media
	 * @return array
	 */
	protected function getMediaParameters(): array {
		return [
		  'id' => $this->id,
		  'ts' => $this->updated_at->timestamp
		];
	}

}
