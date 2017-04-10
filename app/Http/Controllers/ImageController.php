<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Contracts\Filesystem\Filesystem;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class ImageController extends Controller {

	/**
	 * @var \League\Glide\Server
	 */
	protected $server;

	public function __construct(Filesystem $filesystem) {
		$this->server = ServerFactory::create([
		  'response'          => new LaravelResponseFactory(app('request')),
		  'source'            => $filesystem->getDriver(),
		  'cache'             => $filesystem->getDriver(),
		  'cache_path_prefix' => '.cache',
		  'base_url'          => 'img',
		  'driver' => 'imagick',
		]);
	}

	/**
	 * @param Media $media
	 * @return mixed
	 */
	public function show(Media $media) {
		return $this->getResponse($media,[]);
	}

	/**
	 * @param Media $media
	 * @return mixed
	 */
	public function thumbnail(Media $media) {
		return $this->getResponse($media,['h'=>100,'w'=>100,'fit'=>'crop']);
	}

	protected function getResponse(Media $media,array $params){

		$response =  $this->server->getImageResponse($media->path,$params);
		
		$response->headers->set('Content-Disposition',"filename={$media->filename}");

		return $response;
	}

}
