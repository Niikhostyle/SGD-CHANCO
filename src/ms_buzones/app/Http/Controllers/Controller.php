<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //

    protected function respondSuccess($message, $status) //200 - 201
    {
        return response()->json([
            'status' => $status, 
            'data' => [
                'comentario' => $message
        ]], $status);
    }

    protected function respondFail($message) //400
    {
        return response()->json([
            'status' => '400', 
            'data' => [
                'comentario' => $message
            ]], '400');  
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
