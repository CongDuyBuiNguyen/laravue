<?php

namespace App\Http\Controllers;

use App\Laravue\Helpers;
use Illuminate\Http\Request;
use App\Laravue\Models\Business\ToolModel;
use Bschmitt\Amqp\Facades\Amqp;

class ToolController extends Controller
{
    protected $_toolModel;
    protected $_helpers;

    public function __construct(
        ToolModel $toolModel, Helpers $helpers
    )
    {
        $this->_toolModel = $toolModel;
        $this->_helpers = $helpers;
    }

    public function upload(Request $request)
    {
//        $upload_path = public_path('upload');
//        $file_name = $request->file->getClientOriginalName();
//        $generated_new_name = time() . '.' . $request->file->getClientOriginalExtension();
//        $request->file->move($upload_path, $file_name);
        $file_name = 'test';
        Amqp::publish('d41d8cd98f00b204e9800998ecf8427e', 'message',
            [
                'queue'=>'d41d8cd98f00b204e9800998ecf8427e',
                'queue_durable' => 'true',
            ]);
//        $a::publish('d41d8cd98f00b204e9800998ecf8427e', 'message');
        return response()->json(['success' => 'You have successfully uploaded "' . $file_name . '"']);
    }
}
