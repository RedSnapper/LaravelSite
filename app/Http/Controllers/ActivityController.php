<?php

namespace App\Http\Controllers;

use App\Http\Formlets\ActivityFormlet;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityController extends Controller {

	/**
	 * @var ActivityFormlet
	 */
	private $form;

	public function __construct(ActivityFormlet $form) {
		$this->middleware('can:ACTIVITIES_ACCESS');
		$this->form = $form;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Category $category) {
		$activities = Activity::orderBy('name');
		if ($category->exists) {
			$activities->where('category_id', $category->id);
			$activities =  $activities->paginate(10);
			return view("activity.index",compact('activities','category'));
		}
		return view("activity.index");
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 */
	public function create() {
		return $this->form->renderWith(['route' => 'activity.store'])
			->with('title','New Activity');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$activity = $this->form->store();
		return redirect()->route('activity.edit', $activity->id);
	}

	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id) {
		$this->form->setKey($id);
		return $this->form->renderWith([
			'route'  => ['activity.update', $id],
			'method' => 'PUT'
		])
			->with('category',"{$this->form->getModel('activity')->category_id}")
			->with('title',"Edit Activity: {$this->form->getModel('activity')->name}");
	}

	public function update(Activity $activity) {
		$this->authorize('modify', [$activity->category]);
		$this->form->setKey($activity->id);
		$activity = $this->form->update();
		return redirect()->route('activity.edit',$activity->id);
	}

	/**
	 * @param Activity $activity
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function destroy(Activity $activity) {
		$category = $activity->category;
		$this->authorize('modify', [$category]);
		$activity->delete();
		return redirect()->route('activity.index',[$category]);
	}
}
