<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    protected function respondSuccess($message, $status) //200 - 201
    {
        return response()->json([
            'status' => $status, 
            'data' => $message
        ], $status);
    }

    protected function respondFail($message) //400
    {
        $result = array('status' => '400', 'data' => array('comentario' => $message));

        return response()->json($result, '400');  
    }
    
    protected function respondError($message, $status) //406
    {
        return response()->json([
            'status' => $status, 
            'data' => [
                'comentario' => $message
        ]], $status);
    }
        
}
