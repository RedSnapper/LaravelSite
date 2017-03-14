<?php
/**
 * Created by PhpStorm.
 * User: param
 * Date: 10/03/2017
 * Time: 15:31
 */

namespace App\Http\Formlets;

use App\User;
use App\UserProfile;
use Illuminate\Database\Eloquent\Model;

class UserComposite extends Formlet {

	private $creating = false;
	protected $view = "user.composite";
	protected $formView = "user.form";

	public function setCreating(bool $creating = false) {
		$this->creating = $creating;
	}

		public function prepareForm(){
		if($this->creating) {
			$this->addFormlet('user',UserFormlet::class);
		} else {
			$this->addFormlet('user',UserEmailFormlet::class);
		}
		$this->addFormlet('profile',UserProfileForm::class);
	}

	public function edit(): Model {
		$user = $this->models['user'];
		$user->fill($this->request->get('user'))->save();
		$user->profile->fill($this->request->get('profile'))->save(); //remember to set the nominal primary key.
		return $user;
	}


	public function persist():Model {
		$user = User::create($this->request->get('user'));
		$user->profile()->create($this->request->get('profile'));
		return $user;
	}

}