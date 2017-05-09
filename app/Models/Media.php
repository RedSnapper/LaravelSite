<?php

namespace App\Models;

use App\Models\Helpers\Versioned;
use App\Models\Helpers\VersionsInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Laravel\Scout\Searchable;

class Media extends Model implements VersionsInterface {
	use Versioned;
	use Searchable;
	protected $with = ['team', 'category'];
	/**
	 * The attributes that are mass assignable. The rest are key, versions, or derived.
	 * +--------------+
	 * |k id          |
	 * +--------------+
	 * |v prev_id     |
	 * |v next_id     |
	 * +--------------+
	 * |√ category_id |
	 * |√ team_id     |
	 * |√ name        |
	 * |√ filename    |
	 * +--------------+
	 * |d path        |
	 * |d mime        |
	 * |d is_image    |
	 * |d size        |
	 * +--------------+
	 *  protected $fillable = ['category_id', 'team_id','name','path','mime','filename','size'];
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'filename', 'category_id', 'team_id'];
	protected $casts = [
		'properties' => 'array',
		'details'    => 'array',
		'exif'       => 'array'
	];

	/**
	 * Versioned Trait required method
	 *
	 * @return string
	 */
	public function versionsTable(): string {
		return "media_versions";
	}

	public function saveMedia(array $fields, UploadedFile $file = null): Media {
		$this->fill($fields);

		if ($file) {
			$path = $file->store('/media');
			$this->path = $path;
			$filePath = $file->path();
			$this->mime = $file->getMimeType();
			$exifType = @exif_imagetype($filePath);
			$this->is_image = ($exifType !== false);
			$this->size = $file->getSize();
			$this->filename = $file->getClientOriginalName();
			if ($this->is_image) {
				$this->doImageInformation($exifType, $filePath);
			}
		}
		$this->save();

		return $this;
	}

	protected function doImageInformation(int $exifType, string $filePath) {
		$this->exif = null;
		$this->properties = null;
		$this->details = null;
		$this->has_tn = false;
		$image = new \Imagick($filePath);
		$properties = $image->getImageProperties();

		$this->properties = array_filter($properties, function ($key) {
			return explode(':', $key)[0] != "exif";
		}, ARRAY_FILTER_USE_KEY);
		$details = $image->identifyImage();
		foreach ($details as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					$details["$key.$k"] = $v;
				}
				unset($details[$key]);
			}
		}
		$this->details = $details;
		if (in_array($exifType, [2, 7, 8])) {
			$exif = @exif_read_data($filePath, null, true, false);
			unset($exif['EXIF']['MakerNote']);
			foreach ($exif as $set => $details) {
				foreach ($details as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							$details["$key.$k"] = $v;
						}
						unset($details[$key]);
					}
				}
				$exif[$set]=$details;
			}

			$this->exif = $exif;
			$this->has_tn = (@exif_thumbnail($filePath) !== false);
		}
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

	public function scopeTeam($query, $team) {
		return $query->where('team_id', $team);
	}

	public function scopeCategory($query, $category) {
		return $query->where('category_id', $category);
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
