<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserComposite;

use App\Models\User;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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
		$this->authorize('USER_ACCESS');
		$users = $user->orderBy('email')->paginate(10);
		return view("user.index", compact('users'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(User $user) {
		$this->authorize('USER_SHOW');
		$form = $this->form->create(
		  ['route' => 'user.store']
		);
		$form->setModel($user);
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
		$this->authorize('USER_MODIFY');
		return $this->form->renderWith(['route' => ['user.store']])
		  ->with('title','New User');
	}

	/**
	 * Edit is called when wishing to show the record for editing.
	 * @param User     $user
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit(User $user) {
		$this->authorize('USER_MODIFY');
		$this->form->setModel($user);
		//now update method for updating an existing model.
		return $this->form->renderWith(['route'  => ['user.update', $user->getKey()],'method' => 'PATCH'])
		  ->with('title',"Edit User: {$this->form->getModel('user')->name}");
	}

	/**
	 * Store a newly created resource in storage. It is the receiver from create() above.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$this->authorize('USER_MODIFY');
		$user = $this->form->store();
		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * update an existing resource in storage. It is the receiver from edit() above.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(User $user) {
		$this->authorize('USER_MODIFY');
		$this->form->setModel($user);
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
		$this->authorize('USER_DESTROY');
		$this->form->delete($id);
		return redirect()->back();
	}
}
