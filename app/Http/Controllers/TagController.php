<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 11/05/2017 10:04
 */

namespace App\Http\Controllers;

use App\Http\Formlets\TagFormlet;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class TagController extends Controller {
	/**
	 * @var TagFormlet
	 */
	private $form;

	public function __construct(TagFormlet $form) {
		$this->form = $form;
//		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Category $category) {
		$tags = Tag::orderBy('name');
		if ($category->exists) {
			$tags->where('category_id', $category->id);
			$tags =  $tags->paginate(10);
			return view("tag.index",compact('tags','category'));
		}
		return view("tag.index");
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return View
	 */
	public function create(Category $category) {
		if ($category->exists) {
			$this->authorize('modify', $category);
			$form = $this->form->create(['route' => 'tag.store']);
			$form->with('category', $category);
			return $form->render()
				->with('title', 'New Tag')
				->with('category', $category);
		}
	}


	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	public function edit(Tag $tag) {
		$this->authorize('modify', $tag->category);

		$form = $this->form->create(
			['route'  => ['tag.update',$tag ],
			 'method' => 'PUT'
			]);
		$form->setModel($tag);
		return $form->render()
			->with(['title' => "Editing"]);

	}

	public function update(Tag $tag, TagFormlet $form) {
		$this->authorize('modify', $tag->category);
		$form->setModel($tag);
		$tag = $form->update();
		return redirect()->route('tag.edit', $tag->id);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Tag $tag) {
		$this->authorize('modify', $tag->category);
		$category = $tag->category;
		//Now delete the object.
		$tag->delete();
		return redirect()->route('tag.index',[$category]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$tag = $this->form->store();
		return redirect()->route('tag.edit', $tag->id);
	}

}