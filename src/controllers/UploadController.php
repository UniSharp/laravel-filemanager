<?php namespace Jayked\Laravelfilemanager\controllers;

use Jayked\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Lang;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class UploadController extends LfmController
{

	private $default_file_types = [ 'application/pdf' ];
	private $default_image_types = [ 'image/jpeg', 'image/png', 'image/gif' ];

	/**
	 * Upload an image/file and (for images) create thumbnail
	 *
	 * @return string
	 */
	public function upload()
	{
		try
		{
			$res = $this->uploadValidator();
			if( true !== $res )
			{
				return Lang::get( 'laravel-filemanager::lfm.error-invalid' );
			}
		}
		catch( \Exception $e )
		{
			return $e->getMessage();
		}

		$file = Input::file( 'upload' );

		$new_filename = $this->getNewName( $file );

		$dest_path = parent::getPath( 'directory' );

		if( File::exists( $dest_path . $new_filename ) )
		{
			return Lang::get( 'laravel-filemanager::lfm.error-file-exist' );
		}

		$file->move( $dest_path, $new_filename );

		if( 'Images' === $this->file_type )
		{
			$this->makeThumb( $dest_path, $new_filename );
		}

		// upload via ckeditor 'Upload' tab
		if( !Input::has( 'show_list' ) )
		{
			return $this->useFile( $new_filename );
		}

		return 'OK';
	}

	/**
	 * Validation of the uploaded file
	 *
	 * @return bool
	 * @throws \Exception
	 */
	private function uploadValidator()
	{
		// when uploading a file with the POST named "upload"

		$expected_file_type = $this->file_type;
		$is_valid           = false;

		$file = Input::file( 'upload' );
		if( empty( $file ) )
		{
			throw new \Exception( Lang::get( 'laravel-filemanager::lfm.error-file-empty' ) );
		}
		if( !$file instanceof UploadedFile )
		{
			throw new \Exception( Lang::get( 'laravel-filemanager::lfm.error-instance' ) );
		}

		$mimetype = $file->getMimeType();

		if( $expected_file_type === 'Files' )
		{
			$config_name     = 'lfm.valid_file_mimetypes';
			$valid_mimetypes = Config::get( $config_name, $this->default_file_types );
		}
		else
		{
			$config_name     = 'lfm.valid_image_mimetypes';
			$valid_mimetypes = Config::get( $config_name, $this->default_image_types );
		}

		if( !is_array( $valid_mimetypes ) )
		{
			throw new \Exception( 'Config : ' . $config_name . ' is not set correctly' );
		}

		if( in_array( $mimetype, $valid_mimetypes ) )
		{
			$is_valid = true;
		}

		if( false === $is_valid )
		{
			throw new \Exception( Lang::get( 'laravel-filemanager::lfm.error-mime' ) . $mimetype );
		}

		return $is_valid;
	}

	/**
	 * Retrieve a name for the file
	 *
	 * @param $file
	 * @return mixed|string
	 */
	private function getNewName( $file )
	{
		$dest_path = parent::getPath( 'directory' );

		$new_filename = $this->existingFile( $dest_path, $file );

		return $new_filename;
	}

	/**
	 * Check if the file already exists with uniqid()
	 *
	 * @param $path
	 * @param $file
	 * @param null $extension
	 * @return string
	 */
	private function existingFile( $path, $file, $extension = null )
	{
		$name = pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME );

		if( $extension !== null )
		{
			$name .= '_' . $extension;
		}
		$name .= '.' . $file->getClientOriginalExtension();

		// If a file with the name already exists
		if( File::exists( $path . $name ) )
		{
			return self::existingFile( $path, $file, uniqid() );
		}

		return $name;
	}

	/**
	 * Generate a small thumb for the file
	 *
	 * @param $dest_path
	 * @param $new_filename
	 */
	private function makeThumb( $dest_path, $new_filename )
	{
		$thumb_folder_name = Config::get( 'lfm.thumb_folder_name' );

		if( !File::exists( $dest_path . $thumb_folder_name ) )
		{
			File::makeDirectory( $dest_path . $thumb_folder_name );
		}

		$thumb_img = Image::make( $dest_path . $new_filename );
		$thumb_img->fit( 200, 200 )
			->save( $dest_path . $thumb_folder_name . '/' . $new_filename );
		unset( $thumb_img );
	}

	/**
	 * Use the file if it has been uploaded via CKEditor or TinyMCE
	 *
	 * @param $new_filename
	 * @return string
	 */
	private function useFile( $new_filename )
	{
		$file = parent::getUrl() . $new_filename;

		return "<script type='text/javascript'>

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        }

        var funcNum = getUrlParam('CKEditorFuncNum');

        var par = window.parent,
            op = window.opener,
            o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);

        if (op) window.close();
        if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '$file');
        </script>";
	}

}
