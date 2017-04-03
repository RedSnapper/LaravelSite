<?php

namespace App\Http\Controllers;

use App\Http\Transformers\CategoryTransformer;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends ApiController
{

	protected $transformer;

	/**
	 * @var Category
	 */
	private $category;

	/**
	 * CategoriesController constructor.
	 *
	 * @param Category            $category
	 * @param CategoryTransformer $transformer
	 */
	public function __construct(Category $category,CategoryTransformer $transformer) {
		$this->middleware('auth');
		$this->transformer = $transformer;
		$this->category = $category;
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
    	  'parent'=> 'required|exists:categories,id',
		  'name'=> 'required'
		]);

		$category = $this->category->createNode($request->get('parent'),$request->get('name'));

		return $this->respondWithItemCreated($category);
	}

	public function destroy($id){
		Category::destroy($id);
	}
}
