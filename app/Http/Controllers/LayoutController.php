<?php

namespace App\Http\Controllers;

use App\Http\Formlets\LayoutFormlet;
use App\Models\Category;
use App\Models\Layout;
use Illuminate\Http\Request;

class LayoutController extends Controller  {

	/**
	 * @var LayoutFormlet
	 */
	private $form;
	public function __construct(LayoutFormlet $form) {
		$this->form = $form;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Layout $layout) {
		$layouts = $layout->orderBy('name')->paginate(10);
		return view("layout.index", compact('layouts'));
	}


	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Layout $layout) {
		return $this->form->renderWith(['route' => 'layout.store'])
		  ->with('title','New Layout');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$layout = $this->form->store();
		return redirect()->route('layout.edit', $layout->id);
	}

	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id) {
		$this->form->setKey($id);
		return $this->form->renderWith([
			'route'  => ['layout.update', $id],
			'method' => 'PATCH'
		])->with('title',"Edit Layout: {$this->form->getModel()->name}");
	}

	public function update($id) {
		$this->form->setKey($id);
		$layout = $this->form->update();
		return redirect()->route('layout.edit',$layout->id);
	}

	public function cats() {
		return view("layout.cats");
	}
	/**
	 * @return json (this is an api call)
	 */
	public function branch(Request $request) {
		return Category::nodeBranch('LAYOUTS'); //1024 is maximum ancestry.
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
