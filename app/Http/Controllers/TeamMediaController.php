<?php

namespace App\Http\Controllers;

use App\Http\Formlets\MediaFormlet;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamMediaController extends Controller
{
	/**
	 * @var MediaFormlet
	 */
	private $form;

	/**
	 * TeamMediaController constructor.
	 *
	 * @param MediaFormlet $form
	 */
	public function __construct(MediaFormlet $form) {
		$this->form = $form;
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create(Team $team)
    {
		$form = $this->form->create(['route' => 'media.store']);
		$form->with('team',$team);
		return $form->render()->with('title', 'New Media');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
