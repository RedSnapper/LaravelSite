<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;


class CategoriesController extends Controller
{

	/**
	 * CategoriesController constructor.
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	public function index(){
		$categories =  Category::all();

		return Response::json([
		  'data'=>$categories->toArray()
		],200);
	}

	public function show($id){

		$category = Category::find($id);

		if(!$category){
			return Response::json([
			  'errors'=>[
			    'message' => 'Category does not exist'
			  ]
			],404);
		}

		return Response::json([
		  'data'=>$category
		],200);
	}

	public function store(Request $request){

    	$this->validate($request,[
    	  'parent'=> 'required',
		  'name'=> 'required'
		]);

    	Category::create([
    	  'pa'=> $request->get('parent'),
		  'name'=> $request->get('name')
		]);

		return Response::json([
		  'data'=>['message'=>'Category created successfully']
		],201);
	}
}
