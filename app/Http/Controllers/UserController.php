<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserEmailForm;
use App\User;
use Illuminate\Http\Request;
use App\Http\Formlets\UserForm;

class UserController extends Controller {

	/**
	 * @var User
	 */
	private $user;
	/**
	 * @var UserForm
	 */
	private $form;

	public function __construct(User $user,UserForm $form) {
		$this->user = $user;
		$this->form = $form;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$users = $this->user->orderBy('email')->paginate(10);

		return view("user.index", compact('users'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {

		$form = $this->form->create(
		  ['route' => 'user.store']
		)->render();

		return view('user.form', compact('form'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store() {

		$this->form->isValid();

		$user = User::create($this->form->request->only(['name', 'email', 'password']));

		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(\App\Http\Forms\UserForm $form) {
		$form = $form->create(
		  ['route' => 'user.store']
		)->render();

		return view('user.form', compact('form'));
	}

	/**
	 * @param User     $user
	 * @param UserForm $form
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit(User $user, UserEmailForm $form) {

		$form->setModel($user);

		$form = $form->create(
		  [
			'route'  => ['user.update', $user->id],
			'method' => 'PATCH'
		  ]
		)->render();

		return view('user.form', compact('form'));
	}

	public function update(UserEmailForm $form, User $user) {

		$form->setModel($user);
		$form->isValid();

		$user->update($form->fields());

		return redirect()->route('user.index');
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
