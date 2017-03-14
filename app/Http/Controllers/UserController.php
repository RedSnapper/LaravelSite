<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserComposite;
use App\Http\Formlets\UserEmailFormlet;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use App\Http\Formlets\UserFormlet;

class UserController extends Controller {

	/**
	 * @var User
	 */
	private $user;
	/**
	 * @var UserComposite
	 */
	private $form;

	public function __construct(User $user,UserComposite $form) {
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
		$this->form->setCreating(true);
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
		$this->form->setCreating(true);
		$this->form->setModel($user);

		$user = $this->form->store();

		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * @param User     $user
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id) {

		$user = User::with('profile')->find($id);

		$this->form->addModel('user',$user);
		$this->form->addModel('profile',$user->profile);

		return $this->form->create(
		  [
			'route'  => ['user.update', $user->id],
			'method' => 'PATCH'
		  ]
		)->render();

	}

	public function update($id) {
		$user = User::with('profile')->find($id);
		$this->form->addModel('user',$user); //needed for the unique email.
		$this->form->update();

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
