<?php 

namespace Unisharp\Laravelfilemanager\middleware;

use Closure;

class MultiUser
{
    public function handle($request, Closure $next)
    {
    	if (\Config::get('lfm.allow_multi_user') === true) {
    		$slug = \Config::get('lfm.user_field');

	        \Auth::user()->user_field = \Auth::user()->$slug;

	        $base = $request->input('working_dir');

	        if ($base == null) {
	            $request->merge(['working_dir' => \Auth::user()->user_field]);
	        } elseif ($this->wrongDir($base)) {
	            $request->replace(['working_dir' => \Auth::user()->user_field]);
	        }
	    }

        return $next($request);
    }

    private function wrongDir($base)
    {
    	if (strpos($base, \Config::get('lfm.shared_folder_name')) !== false) {
    		return false;
        }

        if (strpos($base, (string)\Auth::user()->user_field) !== false) {
        	return false;
        }

        if (strpos($base, (string)\Auth::user()->user_field) === false) {
            return true;
        }

        return false;
    }
}
