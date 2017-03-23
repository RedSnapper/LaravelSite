<?php

namespace App\Http\Controllers;


use App\Http\Formlets\LayoutFormlet;
use App\Layout;
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
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Layout $layout) {
		return $this->form->renderWith(['route' => 'layout.store']);
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
		]);
	}

	public function update($id) {
		$this->form->setKey($id);
		$layout = $this->form->update();
		return redirect()->route('layout.edit',$layout->id);
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
