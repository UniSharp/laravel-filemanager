<?php

namespace UniSharp\LaravelFilemanager;

interface RepositoryContract
{
    public function rootPath();

    public function directories();

    public function files();

    public function makeDirectory();

    public function exists();

    public function getFile();

    public function mimeType();

    public function isDirectory();

    public function move($new_lfm_path);

    /**
     * Check a folder and its subfolders is empty or not.
     *
     * @param  string  $directory_path  Real path of a directory.
     * @return bool
     */
    public function directoryIsEmpty();
}
