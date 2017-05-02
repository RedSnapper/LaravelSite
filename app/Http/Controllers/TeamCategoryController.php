<?php

namespace App\Http\Controllers;

use App\Http\Transformers\CategoryTransformer;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamCategoryController extends ApiController {
	protected $transformer;

	/**
	 * @var TreeController
	 */
	private $treeController;

	/**
	 * CategoryController constructor.
	 *
	 * @param CategoryTransformer $transformer
	 * @param Category $node
	 */
	public function __construct(CategoryTransformer $transformer, Category $node) {
		$this->middleware('auth');
		$this->transformer = $transformer;
		$this->treeController = new TreeController($node);
	}


	public function index(Team $team,Request $request) {
		return $this->treeController->branch($request->get('section', "ROOT"), $this->allowsAccess($team));
	}

	public function store(Team $team,Request $request) {

		$this->validate($request, [
			'parent' => 'required|category',
			'name' => 'required'
		]);

		$category = $this->treeController->createNode($request->get('parent'), $request->get('name'), $this->allowsModify($team));

		return $this->respondWithItemCreated($category);
	}

	public function update(Team $team, Category $category, Request $request) {

		$this->authorize('modify', [$team, $category]);

		$category->fill($request->all());
		$category->save();

		return $this->respondWithItem($category);
	}

	public function moveInto(Team $team, Category $category, Request $request) {
		$node = $request->get('node');
		if ($this->treeController->moveInto($category, $node, $this->allowsModify($team))) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function moveBefore(Team $team, Category $category, Request $request) {
		$node = $request->get('node');
		if ($this->treeController->moveBefore($category, $node, $this->allowsModify($team))) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function moveAfter(Team $team, Category $category, Request $request) {
		$node = $request->get('node');
		if ($this->treeController->moveBefore($category, $node, $this->allowsModify($team))) {
			return $this->respondWithNoContent();
		}
		return $this->respondForbidden();
	}

	public function destroy(Team $team, Category $category) {

		$this->authorize('modify', [$team, $category]);

		$category->delete();

		return $this->respondWithItem($category);
	}

	private function allowsModify(Team $team) {
		return function (Category $category) use ($team) {
			return Gate::allows('modify', [$team, $category]);
		};
	}

	private function allowsAccess(Team $team) {
		return function (Category $category) use ($team) {
			return Gate::allows('access', [$team, $category]);
		};
	}
}
