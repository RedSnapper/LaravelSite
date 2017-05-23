<?php

namespace App\Http\Controllers;

use App\Http\Transformers\CategoryTransformer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends ApiController {
	protected $transformer;

	/**
	 * @var TreeController
	 */
	private $treeController;

	/**
	 * CategoryController constructor.
	 *
	 * @param CategoryTransformer $transformer
	 * @param Category            $node
	 */
	public function __construct(CategoryTransformer $transformer, Category $node) {
		$this->middleware('auth');
		$this->transformer = $transformer;
		$this->treeController = new TreeController($node);
	}

	public function options(string $reference) {
		return $this->getCollection($reference)->pluck('name', 'id');
	}

	public function getCollection(string $reference) {
		return $this->treeController->options($reference, $this->allowsAccess());
	}

	public function getIds(string $reference){
		return $this->getCollection($reference)->pluck('id');
	}

	public function index(Request $request) {
		return $this->treeController->branch($request->get('section', "ROOT"), $this->allowsAccess());
	}

	public function store(Request $request) {

		$this->validate($request, [
		  'parent' => 'required|category',
		  'name'   => 'required'
		]);

		$category = $this->treeController->createNode($request->get('parent'), $request->get('name'), $this->allowsModify());

		return $this->respondWithItemCreated($category);
	}

	public function update(Category $category, Request $request) {

		$this->authorize('modify',$category);

		$category->fill($request->all());
		$category->save();

		return $this->respondWithItem($category);
	}

	public function moveInto(Category $category, Request $request) {
		if ($this->treeController->moveInto($category, $request->get('node'), $this->allowsModify())) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function moveBefore(Category $category, Request $request) {
		if ($this->treeController->moveBefore($category, $request->get('node'), $this->allowsModify())) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function moveAfter(Category $category, Request $request) {
		if ($this->treeController->moveBefore($category, $request->get('node'), $this->allowsModify())) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function destroy(Category $category) {
		$this->authorize('modify',$category);
		$category->delete();
		return $this->respondWithItem($category);
	}

	private function allowsModify() {
		return function (Category $category) {
			return Gate::allows('modify', $category);
		};
	}

	private function allowsAccess() {
		return function (Category $category) {
			return Gate::allows('access', $category);
		};
	}
}
