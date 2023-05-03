<?php 
namespace VanguardLTE\Http\Requests
{
    abstract class BinaryFileUploadRequest extends Request
    {
        private $fileName = null;
        protected $fs = null;
        protected function prepareForValidation()
        {
            $this->fs = $this->container->make('Illuminate\Contracts\Filesystem\Filesystem');
            $this->files->set($this->fileFieldName(), $this->getUploadedFile());
        }
        protected function fileFieldName()
        {
            return 'file';
        }
        protected function getUploadedFile()
        {
            $this->fileName = str_random(20);
            $this->fs->put($this->fileName, $this->getContent());
            return new \Illuminate\Http\UploadedFile(storage_path('app/' . $this->fileName), $this->fileName, $this->fs->mimeType($this->fileName), null, null, true);
        }
        protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
        {
            if( $this->fs->has($this->fileName) ) 
            {
                $this->fs->delete($this->fileName);
            }
            parent::failedValidation($validator);
        }
    }

}
