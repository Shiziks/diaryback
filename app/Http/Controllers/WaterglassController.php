<?php

namespace App\Http\Controllers;

use App\Models\waterglass;
use Illuminate\Http\Request;

class WaterglassController extends Controller
{
    /////GET ALL WATER GLASSES////////////////////////////////////////////////
    public function getallwaterglasses()
    {
        $glasses = waterglass::select('id', 'glass_number', 'icon')->get();
        if ($glasses) {
            return response()->json([
                'status' => 'success',
                'data' => $glasses
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database errror'
            ], 500);
        }
    }

    /////EDIT WATER GLASSES////////////////////////////////////////////////
    public function editwaterglasses(Request $request)
    {
        $request->validate([
            'id' => 'numeric|min:1',
            'glass_number' => 'string|min:2',
            'icon' => 'string'
        ]);

        if ($request) {
            $id = $request->id;
            $glass_number = $request->glass_number;
            $icon = $request->icon;
            if ($id && $glass_number) {
                $done = waterglass::where('id', '=', $id)->update(['glass_number' => $glass_number]);
                if (!$done) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Database error'
                    ], 500);
                }
            }
            if ($icon) {
                $doneIcon = waterglass::select('icon')->update(['icon' => $icon]);
                if (!$doneIcon) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Database error'
                    ], 500);
                }
            }
            return response()->json(waterglass::all(), 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }
    }
}
