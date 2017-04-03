<?php

namespace App\Http\Controllers;

use App\Http\Transformers\CategoryTransformer;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends ApiController
{

	protected $transformer;

	/**
	 * CategoriesController constructor.
	 */
	public function __construct(CategoryTransformer $transformer) {
		$this->middleware('auth');
		$this->transformer = $transformer;
	}

	public function index(){

		$categories =  Category::all();

		return $this->respondWithCollection($categories);
	}

	public function show($id){

		$category = Category::find($id);

		if(!$category){
			return $this->respondNotFound('Category does not exist');
		}

		return $this->respondWithItem($category);
	}

	public function store(Request $request){

    	$this->validate($request,[
    	  'parent'=> 'required',
		  'name'=> 'required'
		]);

    	$category = Category::create([
    	  'pa'=> $request->get('parent'),
		  'name'=> $request->get('name')
		]);

		return $this->respondWithItemCreated($category);
	}

	public function destroy($id){
		Category::destroy($id);
	}
}
