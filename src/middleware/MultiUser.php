<?php 

namespace Tsawler\Laravelfilemanager\middleware;

use Closure;

class MultiUser
{
    public function handle($request, Closure $next)
    {
    	if (\Config::get('lfm.allow_multi_user') === true) {
	        if ($request->input('base') == null) {
	            $request->merge(['base' => \Auth::user()->name]);
	        } elseif (strpos($request->input('base'), \Auth::user()->name) === false) {
	            $request->replace(['base' => \Auth::user()->name]);
	        }
	    }

        return $next($request);
    }
}
