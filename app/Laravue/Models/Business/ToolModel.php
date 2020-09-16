<?php


namespace App\Laravue\Models\Business;

use App\Laravue\Models\Database\UploadSpleeterAudio;

class ToolModel
{
    protected $_uploadSpleeterAudio;

    public function __construct(
        UploadSpleeterAudio $uploadSpleeterAudio
    )
    {
        $this->_uploadSpleeterAudio = $uploadSpleeterAudio;
    }

    public function storeData($data)
    {
        return $this->_uploadSpleeterAudio->create($data);
    }

    public function getData()
    {
        return $this->_uploadSpleeterAudio->select()->get();
    }

    public function updateFileStatus($data)
    {
        return $this->_uploadSpleeterAudio->where('id', $data)->update(['status' => 'inProgress']);
    }

}
