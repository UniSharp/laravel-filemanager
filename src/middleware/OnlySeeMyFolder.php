<?php 

namespace Tsawler\Laravelfilemanager\middleware;

use Closure;

class OnlySeeMyFolder
{
    public function handle($request, Closure $next)
    {
        if ($request->input('base') == null) {
            $request->merge(['base' => \Auth::user()->name]);
        } elseif (strpos($request->input('base'), \Auth::user()->name) === false) {
            $request->replace(['base' => \Auth::user()->name]);
        }

        return $next($request);
    }
}
