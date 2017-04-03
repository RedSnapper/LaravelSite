<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class CategoriesController extends ApiController
{

	/**
	 * CategoriesController constructor.
	 */
	public function __construct() {
		//$this->middleware('auth');
	}

	public function index(){
		$categories =  Category::all();

		return $this->respond([
		  'data'=>$categories->toArray()
		]);
	}

	public function show($id){

		$category = Category::find($id);

		if(!$category){
			return $this->respondNotFound('Category does not exist');
		}

		return $this->respond([
		  'data'=>$category
		]);
	}

	public function store(Request $request){

    	$this->validate($request,[
    	  'parent'=> 'required',
		  'name'=> 'required'
		]);

    	Category::create([
    	  'pa'=> $request->get('parent'),
		  'name'=> $request->get('name')
		]);

		return $this->setStatusCode(201)->respond([
		  'data'=>['message'=>'Category created successfully']
		]);
	}

	public function destroy($id){
		Category::destroy($id);
	}
}
