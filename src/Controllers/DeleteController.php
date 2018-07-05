<?php

namespace UniSharp\LaravelFilemanager\Controllers;

use Illuminate\Support\Facades\File;
use UniSharp\LaravelFilemanager\Events\ImageIsDeleting;
use UniSharp\LaravelFilemanager\Events\ImageWasDeleted;

/**
 * Class CropController.
 */
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

        $file_to_delete = parent::getCurrentPath($name_to_delete);
        $thumb_to_delete = parent::getThumbPath($name_to_delete);

        event(new ImageIsDeleting($file_to_delete));

        if (is_null($name_to_delete)) {
            return parent::error('folder-name');
        }

        if (! File::exists($file_to_delete)) {
            return parent::error('folder-not-found', ['folder' => $file_to_delete]);
        }

        if (File::isDirectory($file_to_delete)) {
            if (! parent::directoryIsEmpty($file_to_delete)) {
                return parent::error('delete-folder');
            }

            File::deleteDirectory($file_to_delete);

            return parent::$success_response;
        }

        if (parent::fileIsImage($file_to_delete)) {
            File::delete($thumb_to_delete);
        }

        File::delete($file_to_delete);

        event(new ImageWasDeleted($file_to_delete));

        return parent::$success_response;
    }
}
