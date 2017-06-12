<?php namespace Unisharp\Laravelfilemanager\controllers;

use Unisharp\Laravelfilemanager\Events\ImageIsDeleting;
use Unisharp\Laravelfilemanager\Events\ImageWasDeleted;
use Unisharp\FileApi\FileApi;

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
        $working_dir = parent::getCurrentPath();
        $file_to_delete = $working_dir . DIRECTORY_SEPARATOR . $name_to_delete;
        $fa = new FileApi($working_dir);

        event(new ImageIsDeleting($file_to_delete));

        if (is_null($name_to_delete)) {
            return parent::error('folder-name');
        }

        if (!parent::exists($file_to_delete)) {
            return parent::error('folder-not-found', ['folder' => $file_to_delete]);
        }

        if ($fa->isDirectory($name_to_delete)) {
            if (!$fa->directoryIsEmpty($name_to_delete)) {
                return parent::error('delete-folder');
            }

            $fa->deleteDirectory($name_to_delete);

            return parent::$success_response;
        }

        $fa->drop($name_to_delete);

        event(new ImageWasDeleted($file_to_delete));

        return parent::$success_response;
    }
}
