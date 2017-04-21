<?php

namespace App\View\Layouts;

use App\Http\Controllers\CategoryController;
use App\Http\Formlets\LogoutForm;
use App\Models\Team;
use App\Models\User;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use RS\NView\Document;
use RS\NView\View;
use RS\NView\ViewController;

class Navigation extends ViewController{

	/**
	 * @var LogoutForm
	 */
	private $form;
	/**
	 * @var CategoryController
	 */
	private $catController;

	public function __construct(LogoutForm $form, CategoryController $catController) {
		$this->form = $form;
		$this->catController = $catController;
	}

	public function compose(View $view) {

		if(!Auth::check()){
			return;
		}

		$userTeams = Auth::user()->teams()->get();

		$view->with('mediaTeams',$userTeams);
	}

	public function render(Document $view,array $data): Document {
   		$view->set("//*[@data-v.xp='app']/child-gap()",config('app.name'));

   		if(!Auth::guest()){
			$view->set("//*[@data-v.xp='username']",Auth::user()->name);
			$view->set("//*[@data-v.xp='logout']",$this->showLogoutForm());
		}

		$current = Route::current()->getName();
		$view->set("//h:li[.//*[@data-v.route='$current']]/@class/child-gap()"," active");

   		return $view;
   }


	protected function showLogoutForm(){
		return $this->form->create(['route'=>'logout','class'=>'dropdown__form'])->render();
	}


}