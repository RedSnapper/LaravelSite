<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserComposite;
use App\Models\Category;
use App\Models\Team;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Formlets\UserFormlet;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {

	/**
	 * @var UserComposite
	 */
	private $form;
	public function __construct(UserComposite $form) {
		$this->form = $form;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(User $user) {
		$users = $user->orderBy('email')->paginate(10);
		return view("user.index", compact('users'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id = null) {
		$form = $this->form->create(
		  ['route' => 'user.store']
		);
		$form->setKey($id);
		$form->render();
		return view('user.form', compact('form'));
	}

	/**
	 * Show the form for creating a new resource.
	 * This is an empty form.
	 *
	 * @return View
	 */
	public function create($id = null) {
		return $this->form->renderWith(['route' => ['user.store']])
		  ->with('title','New User');
	}

	/**
	 * Edit is called when wishing to show the record for editing.
	 * @param User     $user
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id = null) {
		$this->form->setKey($id);
		//now update method for updating an existing model.
		return $this->form->renderWith(['route'  => ['user.update', $id],'method' => 'PATCH'])
		  ->with('title',"Edit User: {$this->form->getModel('user')->name}");
	}

	/**
	 * Store a newly created resource in storage. It is the receiver from create() above.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$user = $this->form->store();
		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * update an existing resource in storage. It is the receiver from edit() above.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update($id) {
		$this->form->setKey($id);
		$user = $this->form->update();
		return redirect()->route('user.edit',$user->id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$this->form->delete($id);
		return redirect()->back();
	}
}
