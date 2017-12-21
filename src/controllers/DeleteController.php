<?php

namespace UniSharp\LaravelFilemanager\controllers;

use UniSharp\LaravelFilemanager\Events\ImageIsDeleting;
use UniSharp\LaravelFilemanager\Events\ImageWasDeleted;

class DeleteController extends LfmController
{
    /**
     * Delete image and associated thumbnail.
     *
     * @return mixed
     */
    public function getDelete()
    {
        $name_to_delete = request('items');
        $file_to_delete = $this->lfm->pretty($name_to_delete);
        $file_path = $file_to_delete->path('absolute');

        event(new ImageIsDeleting($file_path));

        if (is_null($name_to_delete)) {
            return parent::error('folder-name');
        }

        if (! $this->lfm->setName($name_to_delete)->exists()) {
            return parent::error('folder-not-found', ['folder' => $file_path]);
        }

        if ($this->lfm->setName($name_to_delete)->isDirectory()) {
            if (! $this->lfm->setName($name_to_delete)->directoryIsEmpty()) {
                return parent::error('delete-folder');
            }
        } else {
            if ($file_to_delete->isImage()) {
                $this->lfm->setName($name_to_delete)->thumb()->delete();
            }
        }

        $this->lfm->setName($name_to_delete)->delete();

        event(new ImageWasDeleted($file_path));

        return parent::$success_response;
    }
}
