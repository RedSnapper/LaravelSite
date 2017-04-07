<?php

namespace App\Http\Controllers;

use App\Http\Formlets\MediaFormlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
	/**
	 * @var MediaFormlet
	 */
	private $form;

	public function __construct(MediaFormlet $form) {
		$this->form = $form;
		$this->middleware('auth');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Request $request) {

		$form = $this->form->create(['route' => 'media.store']);
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
		return redirect()->back();
	}

}
