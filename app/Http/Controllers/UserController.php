<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserEmailFormlet;
use App\User;
use Illuminate\Http\Request;
use App\Http\Formlets\UserFormlet;

class UserController extends Controller {

	/**
	 * @var User
	 */
	private $user;
	/**
	 * @var UserFormlet
	 */
	private $form;

	public function __construct(User $user,UserFormlet $form) {
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
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(User $user) {

		$this->form->setModel($user);

		$form = $this->form->create(
		  ['route' => 'user.store']
		)->render();

		return $form;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(User $user,Request $request) {

		$this->form->setModel($user);

		$user = $this->form->store();

		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * @param User     $user
	 * @param UserForm $form
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit(User $user, UserEmailFormlet $form) {

		$form->setModel($user);

		$form = $form->create(
		  [
			'route'  => ['user.update', $user->id],
			'method' => 'PATCH'
		  ]
		)->render();

		return $form;
	}

	public function update(UserEmailFormlet $form, User $user) {

		$form->setModel($user);

		$user = $form->update();

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
