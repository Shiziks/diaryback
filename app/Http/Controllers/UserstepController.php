<?php

namespace App\Http\Controllers;

use App\Models\step;
use App\Models\userstep;
use Illuminate\Http\Request;

class UserstepController extends Controller
{
    /////GET ALL USER STEPS////////////////////////////////////////////////
    public function allusersteps($id)
    {
        $data = userstep::where('user_id', '=', $id)
            ->join('steps', 'usersteps.step_id', '=', 'steps.id')
            ->select('usersteps.user_id', 'usersteps.step_id', 'steps.step_count', 'usersteps.id', 'usersteps.created_at')
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /////SAVE USER STEP COUNT////////////////////////////////////////////////
    public function saveusersteps(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'step_count' => 'required|integer',
        ]);

        if ($request) {
            $tmp = step::create([
                'step_count' => $request->step_count
            ]);
            if ($tmp) {
                $steps_id = $tmp->id;
                $userSteps = userstep::create([
                    'user_id' => $request->user_id,
                    'step_id' => $steps_id
                ]);

                if ($userSteps) {
                    $data = $userSteps;
                    $data['step_count'] = $request->step_count;
                    return response()->json([
                        'status' => 'success',
                        'data' => $data
                    ]);
                }
            }
        }
    }


    /////UPDATE USER STEP COUNT////////////////////////////////////////////////
    public function updateusersteps(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'step_count' => 'required|integer',
            'id' => 'required|integer'
        ]);

        if ($request) {
            $update = step::where('id', '=', $request->id)
                ->update(['step_count' => $request->step_count]);
            if ($update) {
                return response()->json($update);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database errror.'
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
