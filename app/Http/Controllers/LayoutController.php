<?php

namespace App\Http\Controllers;

use App\Http\Formlets\LayoutFormlet;
use App\Models\Category;
use App\Models\Layout;
use Illuminate\Http\Request;

class LayoutController extends Controller {

	/**
	 * @var LayoutFormlet
	 */
	private $form;

	public function __construct(LayoutFormlet $form) {
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

		if ($category) {
			$category = Category::findOrFail($category);
			$layouts = $category->layouts()->orderBy('name')->paginate(10);
			$data = compact('layouts', 'category');
		}
		return view("layout.index", $data);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Request $request) {

		$category = $request->get('category', '');
		$form = $this->form->create(['route' => 'layout.store']);
		$form->with('category', $category);
		return $form->render()->with('title', 'New Layout');
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
		  'method' => 'PUT'
		])->with('title', "Edit Layout: {$this->form->getModel()->name}");
	}

	public function update($id) {
		$this->form->setKey($id);
		$layout = $this->form->update();
		return redirect()->route('layout.edit', $layout->id);
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
