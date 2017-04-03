<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 03/04/2017
 * Time: 09:25
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ApiController extends Controller {

	/**
	 * @var int
	 */
	protected $statusCode = 200;

	/**
	 * @return int
	 */
	public function getStatusCode(): int {
		return $this->statusCode;
	}

	/**
	 * @param int $statusCode
	 * @return $this
	 */
	public function setStatusCode(int $statusCode) {
		$this->statusCode = $statusCode;
		return $this;
	}

	/**
	 * @param string $message
	 * @return mixed
	 */
	public function respondNotFound($message = "Not Found") {
		return $this->setStatusCode(404)->respondWithError($message);
	}

	/**
	 * @param       $data
	 * @param array $headers
	 * @return mixed
	 */
	public function respond($data, $headers = []) {
		return Response::json($data, $this->getStatusCode(), $headers);
	}

	/**
	 * @param $message
	 * @return mixed
	 */
	public function respondWithError($message) {
		return $this->respond([
		  'errors' => [
			'message' => $message
		  ]
		]);
	}

	public function respondWithItem($item,TransformerAbstract $transformer){
		$fractal = new Manager();

		$resource = new Item($item,$transformer);

		return $this->respond($fractal->createData($resource)->toArray());
	}

	public function respondWithCollection($item,TransformerAbstract $transformer){

		$fractal = new Manager();

		$resource = new Collection($item,$transformer);

		return $this->respondWithArray($fractal->createData($resource)->toArray());
	}

	public function respondWithItemCreated($item,TransformerAbstract $transformer) {
		$this->setStatusCode(201);

		return $this->respondWithItem($item,$transformer);
	}

	/**
	 * Returns a json response that contains the specified array,
	 * the current status code and optional headers.
	 *
	 * @param array $array
	 * @param array $headers
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function respondWithArray(array $array, array $headers = []) {
		return response()->json($array, $this->getStatusCode(), $headers);
	}


}