<?php

namespace App\Http\Controllers;

use App\Http\Formlets\TeamFormlet;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TeamController extends Controller {
	/**
	 * @var TeamFormlet
	 */
	private $form;

	public function __construct(TeamFormlet $form) {
		$this->form = $form;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Category $category) {
		$teams = Team::orderBy('name');
		if ($category->exists) {
			$teams->where('category_id', $category->id);
			$teams = $teams->paginate(10);
			return view("team.index", compact('teams', 'category'));
		}
		return view("team.index");
	}

	/**
	 * @param Category $category
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function create(Category $category) {
		if ($category->exists) {
			$this->authorize('modify', $category);
			$form = $this->form->create(['route' => 'team.store']);
			$form->with('category', $category);
			return $form->render()
				->with('name', 'New Team')
				->with('category', $category);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$team = $this->form->store();
		return redirect()->route('team.edit', $team->id);
	}

	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit(Team $team) {
		$this->authorize('modify', [$team->category]);
		$this->form->setModel($team);
		return $this->form->renderWith([
			'route'  => ['team.update', $team->id],
			'method' => 'PUT'
		])
			->with('category', "{$this->form->getModel('team')->category_id}")
			->with('title', "Edit Team: {$this->form->getModel('team')->name}");
	}

	public function update(Team $team) {
		$category = $team->category;
		$this->authorize('modify', [$category]);
		$this->form->setKey($team->id);
		$team = $this->form->update();
		return redirect()->route('team.edit', $team->id);
	}

	public function destroy(Team $team) {
		$category = $team->category;
		$this->authorize('modify', [$category]);
		$team->delete();
		return redirect()->route('team.index',[$category]);
	}


	public function getCollection() {
//		return $this->treeController->options($reference, $this->allowsAccess());
	}
}
