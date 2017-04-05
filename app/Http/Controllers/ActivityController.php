<?php

namespace App\Http\Controllers;

use App\Http\Formlets\ActivityFormlet;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ActivityController extends Controller {

	/**
	 * @var ActivityComposite
	 */
	private $form;

	public function __construct(ActivityFormlet $form) {
		$this->middleware('can:ACTIVITY_NAV');
		$this->form = $form;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$this->authorize('ACTIVITY_INDEX');
		$category = $request->get('category');
		$data = [];

		if($category){
			$category = Category::findOrFail($category);
			$activities = $category->activities()->orderBy('name')->paginate(10);
			$data = compact('activities','category');
		}
		return view("activity.index",$data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 */
	public function create(Activity $activity) {
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
		])->with('title',"Edit Activity: {$this->form->getModel('activity')->name}");
	}

	public function update($id) {
		$this->form->setKey($id);
		$activity = $this->form->update();
		return redirect()->route('activity.edit',$activity->id);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		return redirect()->back();
	}
}
