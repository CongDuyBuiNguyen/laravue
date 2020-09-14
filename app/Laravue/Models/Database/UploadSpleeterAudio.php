<?php

namespace App\Laravue\Models\Database;

use Illuminate\Database\Eloquent\Model;

class UploadSpleeterAudio extends Model
{
    protected $table = "upload_spleeter_audio";
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $connection = 'mysql';
}
