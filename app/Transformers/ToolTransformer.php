<?php

namespace App\Transformers;

use League\Fractal\Resource\Item;

class ToolTransformer
{
    public function transformer($data)
    {
        return new Item($data, function($data){
            return [
                'list_file' => $data
            ];
        });
    }
}
