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
	+-------------+------------------+------+-----+---------+----------------+
	| Field       | Type             | Null | Key | Default | Extra          |
	+-------------+------------------+------+-----+---------+----------------+
	k id          | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
	+-------------+------------------+------+-----+---------+----------------+
	√ category_id | int(10) unsigned | NO   | MUL | NULL    |                |
	√ team_id     | int(10) unsigned | NO   | MUL | NULL    |                |
	√ name        | varchar(255)     | NO   | UNI | NULL    |                |
	√ filename    | varchar(255)     | NO   |     | NULL    |                |
	√ rating      | int(11)          | NO   |     | 0       |                |
	√ license_ta  | text             | YES  |     | NULL    |                |
	+-------------+------------------+------+-----+---------+----------------+
	d path        | varchar(255)     | NO   |     | NULL    |                |
	d mime        | varchar(255)     | NO   |     | NULL    |                |
	d size        | int(11)          | NO   |     | NULL    |                |
	d is_image    | tinyint(1)       | NO   |     | NULL    |                |
	d properties  | longtext         | YES  |     | NULL    |                |
	d details     | longtext         | YES  |     | NULL    |                |
	d exif        | longtext         | YES  |     | NULL    |                |
	d has_tn      | tinyint(1)       | NO   |     | 0       |                |
	d created_at  | timestamp        | YES  |     | NULL    |                |
	d updated_at  | timestamp        | YES  |     | NULL    |                |
	+-------------+------------------+------+-----+---------+----------------+
	v version     | int(10) unsigned | YES  |     | NULL    |                |
	v prev_id     | int(10) unsigned | YES  | MUL | NULL    |                |
	v next_id     | int(10) unsigned | YES  | MUL | NULL    |                |
	+-------------+------------------+------+-----+---------+----------------+
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'filename',  'category_id', 'team_id', 'rating', 'license_ta'];
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

	public function tags() {
		return $this->belongsToMany(Tag::class);
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
			'width'      => @$this->details['geometry']['width'],
			'height'     => @$this->details['geometry']['height'],
			'uploaded_at' => $this->created_at->toDateTimeString(),
			'modified_at' => $this->updated_at->toDateTimeString()
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

