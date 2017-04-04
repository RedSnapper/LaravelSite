<?php

namespace App\Http\Controllers;

use App\Http\Formlets\SegmentFormlet;
use App\Models\Category;
use App\Models\Segment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SegmentController extends Controller
{

	/**
	 * @var SegmentFormlet
	 */
	private $form;
	public function __construct(SegmentFormlet $form) {
		$this->form = $form;
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {

		$category = $request->get('category');
		$data = [];

		if($category){
			$category = Category::findOrFail($category);
			$segments = $category->segments()->orderBy('name')->paginate(10);
			$data = compact('segments','category');
		}

		return view("segment.index",$data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 */
	public function create(Segment $segment) {
		return $this->form->renderWith(['route' => 'segment.store'])
		  ->with('title','New Segment');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$segment = $this->form->store();
		return redirect()->route('segment.edit', $segment->id);
	}

	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id) {
		$this->form->setKey($id);
		return $this->form->renderWith([
			'route'  => ['segment.update', $id],
			'method' => 'PUT'
		])->with('title',"Edit Segment: {$this->form->getModel()->name}");
	}

	/**
	 * @return array (this is an api call)
	 */
	public function branch() {
		return Category::nodeBranch('SEGMENTS');
	}

	public function categories(Category $thing) {

		$integrity = $thing->checkIntegrity();
		return view("segment.categories",compact("integrity"));
	}


	public function update($id) {
		$this->form->setKey($id);
		$segment = $this->form->update();
		return redirect()->route('segment.edit',$segment->id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$this->form->delete($id);

		return redirect()->route('segment.index');
	}

}
