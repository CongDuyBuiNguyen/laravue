<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadSpleeterAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_spleeter_audio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_file');
            $table->string('path_file');
            $table->string('status')->default('uploaded');
            $table->string('user_uploaded')->nullable();
            $table->string('path_vocals')->nullable();
            $table->string('path_accompaniment')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upload_spleeter_audio');
    }
}
