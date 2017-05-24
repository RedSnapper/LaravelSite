<?php

namespace App\Http\Controllers;

use App\Http\Formlets\LayoutFormlet;
use App\Models\Category;
use App\Models\Layout;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LayoutController extends Controller {
	/**
	 * @var LayoutFormlet
	 */
	private $form;

	public function __construct(LayoutFormlet $form) {
		$this->form = $form;
		$this->middleware('auth');
	}


	public function index(Category $category) {
		if ($category->exists) {
			if (Gate::allows('LAYOUTS_ACCESS',$category)) {
				$layouts = Layout::orderBy('name');
				if ($category->exists) {
					$layouts->where('category_id', $category->id);
				}
				$layouts = $layouts->paginate(20);
				return view("layout.index", compact('layouts', 'category'));
			}
		} else {
			if (Gate::allows('LAYOUTS_ACCESS')) {
				return view("layout.index");
			}
		}
	}

	/**
	 * @param Category $category
	 * @return mixed
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function create(Category $category) {
		if ($category->exists) {
			$this->authorize('modify', $category);
			$form = $this->form->create(['route' => 'layout.store']);
			$form->with('category', $category);
			return $form->render()
				->with('name', 'New Layout')
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
		$layout = $this->form->store();
		return redirect()->route('layout.edit', $layout->id);
	}

	public function edit(Layout $layout) {
		$this->authorize('modify', $layout->category);
		$form = $this->form->create(
			['route'  => ['layout.update',$layout ],
			 'method' => 'PUT'
			]);
		$form->setModel($layout);
		return $form->render()
			->with('title', "Layout: '$layout->name'")
			->with('category', $layout->category);
	}

	public function update($id) {
		$this->form->setKey($id);
		$layout = $this->form->update();
		return redirect()->route('layout.edit', $layout->id);
	}

	/**
	 * @param Layout $layout
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
	public function destroy(Layout $layout) {
		$category = $layout->category;
		$this->authorize('modify', [$category]);
		$layout->delete();
		return redirect()->route('layout.index',[$category]);
	}
}
