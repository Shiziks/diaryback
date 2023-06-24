<?php

namespace App\Http\Controllers;

use App\Models\UserWater;
use App\Models\waterglass;
use Illuminate\Http\Request;

class UserWaterController extends Controller
{

    /////GET USER WATER LOGS////////////////////////////////////////////////
    public function getallwaterlogs($user_id)
    {
        if (is_numeric($user_id)) {
            $allLogs = UserWater::where('user_id', '=', $user_id)
                ->join('waterglasses', 'waterglasses.id', '=', 'user_waters.waterglass_id')
                ->select('user_waters.id', 'user_waters.user_id', 'user_waters.waterglass_id', 'waterglasses.glass_number', 'user_waters.created_at')
                ->orderBy('user_waters.created_at', 'DESC')
                ->get();
            if ($allLogs) {
                return response()->json([
                    'status' => 'success',
                    'data' => $allLogs
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }
    }

    /////LOG USER WATER INTAKE////////////////////////////////////////////////
    public function logwaterintake(Request $request)
    {
        $request->validate([
            'waterglass_number' => 'required|string',
            'user_id' => 'required|numeric'
        ]);

        if ($request) {
            $glassNumber = $request->waterglass_number;
            $waterglass_id = waterglass::where('glass_number', '=', $glassNumber)->select('id')->first();

            $logwater = UserWater::create([
                'user_id' => $request->user_id,
                'waterglass_id' => $waterglass_id->id
            ]);

            if ($logwater) {
                $logwater['glass_number'] = $glassNumber;
                return response()->json([
                    'status' => 'success',
                    'data' => $logwater
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }

    /////UPDATE USER WATER INTAKE////////////////////////////////////////////////
    public function updatewaterintake(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'waterglass_number' => 'required|string',
            'userwater_id' => 'required|numeric'
        ]);

        if ($request) {
            $glassNumber = $request->waterglass_number;
            $waterglass_id = waterglass::where('glass_number', '=', $glassNumber)->select('id')->first();

            $updated = UserWater::where('id', '=', $request->userwater_id)
                ->update(['waterglass_id' => $waterglass_id->id]);

            if ($updated) {
                $data = [
                    'id' => $request->userwater_id,
                    'user_id' => $request->user_id,
                    'waterglass_id' => $waterglass_id->id,
                    'glass_number' => $request->waterglass_number,
                    'created_at' => date('Y-m-d h:i:s')
                ];
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }
    }
}
