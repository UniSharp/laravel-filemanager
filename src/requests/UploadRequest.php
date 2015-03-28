<?php namespace Tsawler\Laravelfilemanager\requests;

use App\Http\Requests\Request;

class UploadRequest extends Request {

    /**
     * Validation rules for signing up for free trial or registering for paid account.
     *
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file_to_upload' => 'required|image',
        ];
    }

}
