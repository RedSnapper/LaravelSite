<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class ShowSQL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
			if (!is_null($request->sql)) {
				DB::listen(function ($sql) {
					print("<code>" . $sql->sql . ';  ' . print_r($sql->bindings, true) . "</code><br />");
				});
			}

			return $next($request);
    }
}
