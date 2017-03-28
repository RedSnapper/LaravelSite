<?php

namespace App\Http\Middleware;

use Closure;

class RequestMethod {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {

		$method = explode(":",$request->get("_method"));

		if(count($method) > 1){
			$request->merge(['_method'=>$method[0],'_fn'=>$method]);
		}

		return $next($request);
	}
}
