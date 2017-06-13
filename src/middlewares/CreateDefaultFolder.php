<?php

namespace Unisharp\Laravelfilemanager\middlewares;

use Unisharp\Laravelfilemanager\traits\LfmHelpers;
use Closure;

class CreateDefaultFolder
{
    use LfmHelpers;

    public function handle($request, Closure $next)
    {
        $this->initHelper();
        $this->checkDefaultFolderExists('user');
        $this->checkDefaultFolderExists('share');

        return $next($request);
    }

    private function checkDefaultFolderExists($type = 'share')
    {
        if (!$this->allowFolderType($type)) {
            return;
        }

        $path = $this->getRootFolderPath($type);

        $this->createFolderByPath($path);
    }
}
