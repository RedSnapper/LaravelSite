<?php

namespace App\Http\Controllers;

use App\Http\Formlets\MediaEditFormlet;
use App\Http\Formlets\MediaFormlet;
use App\Models\Category;
use App\Models\Media;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaController extends Controller {
	/**
	 * @var MediaFormlet
	 */
	private $form;

	public function __construct(MediaFormlet $form) {
		$this->form = $form;
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Category $category) {

		if ($category->exists) {

			$this->authorize('MEDIA_INDEX',$category);

			$medias = Media::orderBy('name');
			$medias->where('category_id', $category->id);
			$medias = $medias->paginate(10);

			return view("media.index", compact('medias', 'category'));
		}

		$this->authorize('MEDIA_INDEX');

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
		$form = $this->form->create(['route' => 'media.store']);

		$category = $request->get('category');
		$form->with('category', $category);

		return $form->render()->with('title', 'New Media');
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
