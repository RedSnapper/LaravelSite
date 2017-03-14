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

	public function prepareModels() {
		$user = User::with('profile')->find($this->getKey());
		$this->addModel('user',$user); //needed for the unique email.
		$this->addModel('profile',$user->profile);
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
		$user->fill($this->fields('user'))->save();
		$user->profile->fill($this->fields('profile'))->save(); //remember to set the nominal primary key.
		return $user;
	}

	public function persist():Model {
		$user = User::create($this->fields('user'));
		$user->profile()->create($this->fields('profile'));
		return $user;
	}

}