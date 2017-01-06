<?php

namespace Unisharp\Laravelfilemanager\middlewares;

use Closure;
use Unisharp\Laravelfilemanager\traits\LfmHelpers;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

class CreateDefaultFolder
{
    use LfmHelpers;

    public function handle($request, Closure $next)
    {
        $this->checkDefaultFolderExists('user');
        $this->checkDefaultFolderExists('share');

        return $next($request);
    }

    private function checkDefaultFolderExists($type = 'share')
    {
        if ($type === 'user' && \Config::get('lfm.allow_multi_user') !== true) {
            return;
        }

        $path = $this->getPath($type);

        if (!File::exists($path)) {
            File::makeDirectory($path, $mode = 0777, true, true);
        }
    }
}
