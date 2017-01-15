<?php namespace Unisharp\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\File;
use Unisharp\Laravelfilemanager\Events\ImageIsDeleting;
use Unisharp\Laravelfilemanager\Events\ImageWasDeleted;

/**
 * Class CropController
 * @package Unisharp\Laravelfilemanager\controllers
 */
class DeleteController extends LfmController
{
    /**
     * Delete image and associated thumbnail
     *
     * @return mixed
     */
    public function getDelete()
    {
        $name_to_delete = request('items');

        $file_to_delete = parent::getCurrentPath($name_to_delete);
        $thumb_to_delete = parent::getThumbPath($name_to_delete);

        event(new ImageIsDeleting($file_to_delete));

        if (!File::exists($file_to_delete)) {
            return $this->error('folder-not-found', ['folder' => $file_to_delete]);
        }

        if (File::isDirectory($file_to_delete)) {
            if (!parent::directoryIsEmpty($file_to_delete)) {
                return $this->error('delete-folder');
            }

            File::deleteDirectory($file_to_delete);

            return 'OK';
        }

        File::delete($file_to_delete);

        if ($this->isProcessingImages()) {
            File::delete($thumb_to_delete);
        }

        event(new ImageWasDeleted($file_to_delete));

        return 'OK';
    }
}
