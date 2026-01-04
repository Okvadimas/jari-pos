<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
    	$response = [
            'status' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
    	$response = [
            'status'    => false,
            'data'      => [],
            'message'   => $error,
        ];

        return response()->json($response, $code);
    }

    public function ajaxResponse($status, $message, $result=[])
    {
    	$response = [
            'status' => $status,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response);
    }

    public function logs($uid_order, $uid_divisi, $status = 1)
    {
        $user   = Auth::user();

        $logs = Logs::create([
            'uid'           => 'L'.date('YmdHis').mt_rand(100000, 999999),
            'uid_order'     => $uid_order,
            'uid_divisi'    => $uid_divisi,
            'status'        => $status,
            'insert_by'     => $user->username
        ]);

        return $logs;
    }
}
