<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
// use App\Models\Logs; // TODO: Uncomment when Logs model is created

abstract class Controller
{
    /**
     * Return success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message, $code = 200)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Return error JSON response
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code = 400)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => [],
        ], $code);
    }

    /**
     * Create activity log
     * TODO: Enable this function when Logs model is created
     *
     * @param string $uid_order
     * @param string $uid_divisi
     * @param int $status
     * @return mixed
     */
    // public function logs($uid_order, $uid_divisi, $status = 1)
    // {
    //     $user = Auth::user();

    //     $logs = Logs::create([
    //         'uid'           => 'L'.date('YmdHis').mt_rand(100000, 999999),
    //         'uid_order'     => $uid_order,
    //         'uid_divisi'    => $uid_divisi,
    //         'status'        => $status,
    //         'insert_by'     => $user->username
    //     ]);

    //     return $logs;
    // }
}

