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
	protected $fillable = ['name', 'filename', 'category_id', 'team_id', 'license_ta'];
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
		$props = [];
		foreach ($properties as $key => $value) {
			$kb = explode(':', $key);
			if ($kb[0] != "exif") {
				if (isset($kb[1])) {
					$props[$kb[0]][$kb[1]] = $value;
				} else {
					$props[$kb[0]] = $value;
				}
			}
		}
		$this->properties = $props;
		$details = $image->identifyImage();
		unset($details['imageName']);
		$this->details = $details;
		if (in_array($exifType, [2, 7, 8])) {
			$exif = $this->sanitise( @exif_read_data($filePath, null, true, false) ?? [] );
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
			'licensing'  => $this->license_ta,
			'category'   => $this->category->name,
			'exif'   		 => $this->exif,
			'properties' => $this->properties,
			'details'    => $this->details,
			'image'      => $this->is_image,
			'created_at' => $this->created_at->toDateTimeString(),
			'updated_at' => $this->updated_at->toDateTimeString()
		];
	}

	private function utf8It(string $value): string {
		return preg_replace_callback('/((?:[\x20-\x7F] 
		| [\xC0-\xDF][\x80-\xBF] 
		| [\xE0-\xEF][\x80-\xBF]{2} 
		| [\xF0-\xF7][\x80-\xBF]{3})+)/u',
			function ($matches) {
				return "0x" . bin2hex($matches[0]);
			}, $value) ?? "";
	}

	private function sanitise(array $basis) {
		$conversions = [
			"UndefinedTag:0xA434" => "LensModel",
			"UndefinedTag:0xA433" => "LensMake",
			"UndefinedTag:0xA432" => "LensSpec. (FLen Range; FNo. Range)",
			"UndefinedTag:0x001F" => "GPSHPositioningError",
		];
		foreach ($basis as $key => $value) {
			if(is_array($value)) {
				$value = $this->sanitise($value);
			} else {
				$value = $this->utf8It($value);
			}
			$uKey = $this->utf8It($key);
			$uKey = @$conversions[$uKey] ?? $uKey;
			if ($uKey != $key) {
				unset($basis[$key]);
			}
			if ($value != "") {
				$basis[$uKey] = $value;
			} else {
				unset($basis[$uKey]);
			}
		}
		return $basis;
	}
}

