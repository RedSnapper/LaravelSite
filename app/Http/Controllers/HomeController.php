<?php

namespace App\Http\Controllers;
use App\User;

use Illuminate\Support\Facades\DB;
use RS\NView\Factory as ViewFactory;

class HomeController extends Controller
{
	/**
	 * @var ViewFactory
	 */
	private $viewFactory;

	/**
	 * HomeController constructor.
	 *
	 * @param ViewFactory $viewFactory
	 */
	public function __construct(ViewFactory $viewFactory) {
		$this->viewFactory = $viewFactory;
	}

	public function index(){

		$user = User::first();
		$title = "Home Page";

		//$view = $this->viewFactory->make('pages.home',['test'=>['name'=>'Param']]);

		$view = view('pages.home',['test'=>['name'=>'Param']]);

		$view->with(compact('user','title'));


		return $view;
	}
}
