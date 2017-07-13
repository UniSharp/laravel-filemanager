<?php

namespace UniSharp\LaravelFilemanager\middlewares;

use Closure;
use UniSharp\LaravelFilemanager\Lfm;
use UniSharp\LaravelFilemanager\LfmPath;
use UniSharp\LaravelFilemanager\LfmStorage;

class CreateDefaultFolder
{
    private $lfm;
    private $helper;

    public function __construct()
    {
        $lfm = app(LfmPath::class);
        $lfm->helper->setStorage(new LfmStorage($lfm));
        $this->lfm = $lfm;
        $this->helper = app(Lfm::class);
    }

    public function handle($request, Closure $next)
    {
        $this->checkDefaultFolderExists('user');
        $this->checkDefaultFolderExists('share');

        return $next($request);
    }

    private function checkDefaultFolderExists($type = 'share')
    {
        if (! $this->helper->allowFolderType($type)) {
            return;
        }

        $this->lfm->dir($this->helper->getRootFolder($type))->createFolder();
    }
}
