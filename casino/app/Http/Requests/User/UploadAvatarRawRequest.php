<?php 
namespace VanguardLTE\Http\Requests\User
{
    class UploadAvatarRawRequest extends \VanguardLTE\Http\Requests\BinaryFileUploadRequest
    {
        public function rules()
        {
            return ['file' => 'required|image'];
        }
        public function messages()
        {
            return ['file.required' => 'The file is required.'];
        }
    }

}
