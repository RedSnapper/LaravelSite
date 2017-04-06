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

	public function index(Request $request){
		return Category::branch($request->get('section',"ROOT"));
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

	public function update($id,Request $request){

		$category = Category::find($id);

		if(!$category){
			return $this->respondNotFound('Category does not exist');
		}

		$category->fill($request->all());

		$category->save();

		return $this->respondWithItem($category);
	}

	public function moveTo(Category $category,Request $request){

		$category->moveTo($request->get('parent'),$request->get('index'));

		return $this->respondWithItem($category);
	}

	public function moveInto(Category $category,Request $request){
		return $this->respondWithArray([$category->moveInto($request->get('node'))]);
	}

	public function moveBefore(Category $category,Request $request){
		return $this->respondWithArray([$category->moveBefore($request->get('node'))]);
	}

	public function moveAfter(Category $category,Request $request){
		return $this->respondWithArray([$category->moveAfter($request->get('node'))]);
	}

	public function destroy($id){
		$category = Category::find($id);

		if(!$category){
			return $this->respondNotFound('Category does not exist');
		}

		$category->delete();

		return $this->respondWithItem($category);
	}


}
