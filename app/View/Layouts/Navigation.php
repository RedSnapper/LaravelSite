<?php

namespace App\View\Layouts;

use App\Http\Formlets\LogoutForm;
use Illuminate\Support\Facades\Auth;
use RS\NView\Document;
use RS\NView\ViewController;

class Navigation extends ViewController{

	/**
	 * @var LogoutForm
	 */
	private $form;

	public function __construct(LogoutForm $form) {
		$this->form = $form;
	}


	public function render(Document $view,array $data): Document {
   		$view->set("//*[@data-v.xp='app']/child-gap()",config('app.name'));

   		if(Auth::guest()){

		}else{
			$view->set("//*[@data-v.xp='username']",Auth::user()->name);
			$view->set("//*[@data-v.xp='logout']",$this->showLogoutForm());
		}

   		return $view;
   }


	protected function showLogoutForm(){
		return $this->form->create(['route'=>'logout'])->render();
	}


}