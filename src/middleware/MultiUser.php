<?php 

namespace Tsawler\Laravelfilemanager\middleware;

use Closure;

class MultiUser
{
    public function handle($request, Closure $next)
    {
    	if (\Config::get('lfm.allow_multi_user') === true) {
	        if ($request->input('base') == null) {
	            $request->merge(['base' => config('lfm.user_field')]);
	        } elseif (strpos($request->input('base'), config('lfm.user_field')) === false) {
	            $request->replace(['base' => config('lfm.user_field')]);
	        }
	    }

        return $next($request);
    }
}
