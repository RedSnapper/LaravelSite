<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserEmailForm;
use App\User;
use Illuminate\Http\Request;
use App\Http\Formlets\UserForm;

class UserController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(UserForm $form) {

		$form = $form->create(
		  ['route' => 'user.store']
		)->render();

		return view('pages.user', compact('form'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(UserForm $form) {

		$form->isValid();

		$user = User::create($form->request->only(['name','email','password']));

		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
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

		return view('pages.user', compact('form'));
	}


	public function update(UserEmailForm $form, User $user) {

		$form->setModel($user);
		$form->isValid();

		$user->update($form->fields());

		return redirect()->back();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
