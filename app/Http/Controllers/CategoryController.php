<?php

namespace App\Http\Controllers;

use App\Http\Transformers\CategoryTransformer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends ApiController {
	protected $transformer;
	/**
	 * @var Category
	 */
	private $category;
	/**
	 * @var TreeController
	 */
	private $treeController;
	/**
	 * @var \Closure
	 */
	private $allowsModify, $allowsView;

	/**
	 * CategoryController constructor.
	 *
	 * @param Category            $category
	 * @param CategoryTransformer $transformer
	 */
	public function __construct(Category $category, CategoryTransformer $transformer, Category $node) {
		$this->middleware('auth');
		$this->transformer = $transformer;
		$this->category = $category;
		$this->treeController = new TreeController($node);

		$this->allowsModify = function (Category $category) {
			return Gate::allows('update', $category);
		};
		$this->allowsView = function (Category $category) {
			return Gate::allows('view', $category);
		};
	}

	public function options(string $reference) {
		return $this->treeController->options($reference,$this->allowsView);
	}

	public function index(Request $request) {
		return $this->treeController->branch($request->get('section', "ROOT"));
	}

	public function show($id) {

		$category = Category::find($id);

		if (!$category) {
			return $this->respondNotFound('Category does not exist');
		}

		return $this->respondWithItem($category);
	}

	public function store(Request $request) {

		$this->validate($request, [
			'parent' => 'required|exists:categories,id',
			'name'   => 'required'
		]);

		$category = $this->treeController->createNode($request->get('parent'), $request->get('name'), $this->allowsModify);

		return $this->respondWithItemCreated($category);
	}

	public function update($id, Request $request) {

		$category = Category::find($id);

		if (!$category) {
			return $this->respondNotFound('Category does not exist');
		}
		$category->fill($request->all());
		if (($this->allowsModify)($category)) {
			$category->save();
			return $this->respondWithItem($category);
		}
		return $this->respondForbidden();
	}

	public function moveInto(Category $category, Request $request) {
		if ($this->treeController->moveInto($category, $request->get('node'), $this->allowsModify)) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function moveBefore(Category $category, Request $request) {
		if ($this->treeController->moveBefore($category, $request->get('node'), $this->allowsModify)) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function moveAfter(Category $category, Request $request) {
		if ($this->treeController->moveBefore($category, $request->get('node'), $this->allowsModify)) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function destroy($id) {
		$category = Category::find($id);
		if (!$category) {
			return $this->respondNotFound('Category does not exist');
		}
		if (($this->allowsModify)($category)) {
			$category->delete();
			return $this->respondWithItem($category);
		}
		return $this->respondForbidden();
	}
}
