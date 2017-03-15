<?php

namespace App\Http\Controllers;

use App\Http\Formlets\UserComposite;
use App\Http\Formlets\UserEmailFormlet;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use App\Http\Formlets\UserFormlet;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {

	/**
	 * @var UserComposite
	 */
	private $form;
//		UserFormlet / UserComposite
	public function __construct(UserComposite $form) {
		//DB::listen(function($sql) {
		//	print("<code>" . $sql->sql . '  ' . print_r($sql->bindings,true) . "</code><br />" );
		//});
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
		return $this->form->renderWith(['route' => 'user.store']);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$this->form->setCreating(true);
		$user = $this->form->store();
		return redirect()->route('user.edit', $user->id);
	}

	/**
	 * @param User     $user
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id) {
		$this->form->setKey($id,'user');
		return $this->form->renderWith([
			'route'  => ['user.update', $id],
			'method' => 'PATCH'
		]);
	}

	public function update($id) {
		$this->form->setKey($id,'user');
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
		return redirect()->back();
	}
}
