<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Helpers;
use Illuminate\Http\Request;
use App\Laravue\Models\Business\ToolModel;
use App\Transformers\ToolTransformer;
use Bschmitt\Amqp\Facades\Amqp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use League\Fractal\Manager;

class ToolController extends BaseController
{
    protected $_toolModel;
    protected $_toolTransformer;
    protected $_helpers;
    protected $_fractal;

    public function __construct(
        ToolModel $toolModel, ToolTransformer $toolTransformer, Helpers $helpers, Manager $manager
    )
    {
        $this->_toolModel = $toolModel;
        $this->_toolTransformer = $toolTransformer;
        $this->_helpers = $helpers;
        $this->_fractal = $manager;
    }

    public function uploadFile(Request $request)
    {
        $requestParams = $this->_helpers->parseRequest($request->all());
        $uploadPath = public_path('upload');
        $fileName = $requestParams['file']->getClientOriginalName();
        $requestParams['file']->move($uploadPath, $fileName);
        $data = [
            'path_file' => $uploadPath . '/' . $fileName,
            'name_file' => $fileName,
            'user_uploaded' => $requestParams['userUploaded'],
            'created_at' => Carbon::now('Asia/Ho_Chi_Minh'),
        ];

        return $this->_toolModel->storeData($data);
    }

    public function show(Request $request)
    {
        $data = $this->_toolModel->getData();
        $transformData = $this->_toolTransformer->transformer($data);
        $responseData = $this->_fractal->createData($transformData)->toArray();
        $responseData['status'] = 200;
        return $responseData;
    }

    public function downloadFile(Request $request)
    {
        $requestParams = $this->_helpers->parseRequest($request->all());
        return Response::download($requestParams['pathFile']);
    }

    public function sendWorkerMessage(Request $request)
    {
        $requestParams = $this->_helpers->parseRequest($request->all());
        $message = json_decode($requestParams['message']);
        Amqp::publish(
            'd41d8cd98f00b204e9800998ecf8427e',
            json_encode($message),
            [
                'queue'=>'d41d8cd98f00b204e9800998ecf8427e',
                'queue_durable' => 'true',
            ]
        );
        return $this->_toolModel->updateFileStatus($message->id);
    }
}
