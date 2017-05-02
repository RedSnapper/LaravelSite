<?php

namespace App\Http\Controllers;

use App\Http\Formlets\MediaEditFormlet;
use App\Http\Formlets\MediaFormlet;
use App\Models\Category;
use App\Models\Media;
use App\Models\Team;
use Illuminate\Http\Request;

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
	public function index(Team $team, Category $category) {

		if (!$category->exists) {
			$this->authorize('MEDIA_ACCESS');
			return view("media.index", compact('team'));
		}

		$this->authorize('access',[$team, $category]);

		$medias = Media::orderBy('name')
			->team($team->id)
			->category($category->id)
			->paginate(10);

		return view("media.index", compact('team', 'medias', 'category'));
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

	public function create(Team $team, Category $category) {
		if ($category->exists && $team->exists) {
			$this->authorize('access', [$team, $category]);

			$form = $this->form->create(['route' => 'media.store']);
			$form
				->with('category', $category)
				->with('team', $team);

			return $form->render()
				->with('title', 'New Media')
				->with('category', $category)
				->with('team', $team);
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
	public function edit(Media $medium, MediaEditFormlet $form) {

		// Medium is the generated name by Laravel
		// If we want model binding we need to use this name or change it
		// in the routes file

		$this->authorize('modify', [$medium->category, $medium->team]);
		$form->setModel($medium);
		return $form->renderWith([
			'route'  => ['media.update', $medium->id],
			'method' => 'PUT'
		])
			->with('media', $medium)
			->with('category', $medium->category)
			->with('team', $medium->team);
	}

	public function update(Media $medium, MediaEditFormlet $form) {

		$this->authorize('modify', [$medium->category, $medium->team]);
		$form->setModel($medium);
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
