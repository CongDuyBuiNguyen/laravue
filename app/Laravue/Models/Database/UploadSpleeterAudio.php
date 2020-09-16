<?php

namespace App\Laravue\Models\Database;

use Illuminate\Database\Eloquent\Model;

class UploadSpleeterAudio extends Model
{
    protected $table = "upload_spleeter_audio";
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $connection = 'mysql';
    protected $fillable = [
        'id',
        'name_file',
        'path_file',
        'status',
        'user_uploaded',
        'path_vocals',
        'path_accompaniment',
        'created_at',
    ];
}
