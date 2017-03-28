<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class ShowSQL {
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

		if (!is_null($request->sql)) {
			DB::listen(function ($sql) {
				print("<code>" . $sql->sql . ';  ' . print_r($sql->bindings, true) . "</code><br />");
			});
		}

		return $next($request);
	}
}
