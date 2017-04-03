<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 03/04/2017
 * Time: 09:25
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

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
	public function respond($data, $headers=[]){
		return Response::json($data,$this->getStatusCode(),$headers);
	}

	/**
	 * @param $message
	 * @return mixed
	 */
	public function respondWithError($message){
		return $this->respond([
		  'errors' => [
			'message' => $message
		  ]
		]);
	}

}