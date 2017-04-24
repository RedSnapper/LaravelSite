<?php

namespace App\Http\Controllers;

use App\Http\Formlets\MediaEditFormlet;
use App\Http\Formlets\MediaFormlet;
use App\Models\Category;
use App\Models\Media;
use App\Models\Team;
use App\Policies\Helpers\UserPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller {
	/**
	 * @var MediaFormlet
	 */
	private $form;
	/**
	 * @var UserPolicy
	 */
	private $userPolicy;
	///**
	// * @var CategoryController
	// */
	//private $categoryController;

	public function __construct(MediaFormlet $form,UserPolicy $userPolicy) {
		$this->form = $form;
		$this->middleware('auth');
//		$this->categoryController = $categoryController;
//		public function getAvailableTeamCategories($user)
		$this->userPolicy = $userPolicy;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Team $team,Category $category) {

		if (!$category->exists){
			$this->authorize('MEDIA_ACCESS',$team);
			return view("media.index",compact('team'));
		}



////		$teamCats = $this->userPolicy->getAvailableTeamCategories(auth()->user()); 									//teams that I am in.
//		if ($category->exists && Gate::allows('MEDIA_ACCESS', [$category, $team])) {
//			$medias = Media::orderBy('name');
////			$medias->whereIn('team_id',$teamIds)->where('category_id', $category->id);
//			$medias = $medias->paginate(10);
//			return view("media.index", compact('medias', 'category'));
//		} elseif ($category->exists && Gate::allows('MEDIA_ACCESS', [$team,$category])) {
//			$medias = Media::orderBy('name');
////			$medias->whereIn('category_id',$teamIds)->where('team_id', $category->id);
//			$medias = $medias->paginate(10);
//			return view("media.index", compact('medias', 'category'));
//		}

		return view("media.index");
	}

	public function show(Media $medium) {
		//$file = Storage::get("{$medium->path}");
		//
		//$response = response()->make($file, 200,[
		//  'Content-Type'=>$medium->mime,
		//  'Content-Disposition'=>"filename={$medium->filename}"
		//]);
		//
		//return $response;
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Request $request) {
		$requestCategory = $request->get('category');
		if (!is_null($requestCategory)) {
			$category = Category::find($requestCategory);
			$this->authorize('MEDIA_CREATE', $category);
			if ($category->exists) {
				$form = $this->form->create(['route' => 'media.store']);
				$form->with('category',$category->id);
				return $form->render()->with('title', 'New Categorised Media');
			}
		} else {
			$this->authorize('MEDIA_CREATE');
			$form = $this->form->create(['route' => 'media.store']);
			return $form->render()->with('title', 'New Media');
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store() {
		$media = $this->form->store();
		return redirect()->route('media.edit', $media->id);
	}

	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit($id, MediaEditFormlet $form) {
		$form->setKey($id);
		return $form->renderWith([
			'route'  => ['media.update', $id],
			'method' => 'PUT'
		])->with('media', $form->getModel());
	}

	public function update($id, MediaEditFormlet $form) {
		$form->setKey($id);
		$media = $form->update();
		return redirect()->route('media.edit', $media->id);
	}

	public function search(Request $request) {

		$query = $request->get('query');

		$medias = [];

		if ($query) {
			$medias = Media::search($query)->paginate(10);
		}

		return view("media.search", compact('medias', 'query'));
	}
}
