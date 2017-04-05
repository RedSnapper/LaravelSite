<?php

namespace App\Http\Controllers;

use App\Http\Formlets\RoleComposite;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RoleController extends Controller {

	/**
	 * @var RoleComposite
	 */
	private $form;

	public function __construct(RoleComposite $form) {
		$this->form = $form;
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
			$roles = $category->roles()->orderBy('name')->paginate(10);
			$data = compact('roles','category');
		}
		return view("role.index",$data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 */
	public function create(Role $role) {
		return $this->form->renderWith(['route' => 'role.store'])
		  ->with('title','New Role');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$role = $this->form->store();
		return redirect()->route('role.edit', $role->id);
	}

	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id) {
		$this->form->setKey($id);
		return $this->form->renderWith([
			'route'  => ['role.update', $id],
			'method' => 'PUT'
		])->with('title',"Edit Role: {$this->form->getModel('role')->name}");
	}

	public function branch() {
		return Category::nodeBranch('ROLES');
	}

	public function categories(Category $thing) {
		$integrity = $thing->checkIntegrity();
		return view("role.categories",compact("integrity"));
	}


	public function update($id) {
		$this->form->setKey($id);
		$role = $this->form->update();
		return redirect()->route('role.edit',$role->id);
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
