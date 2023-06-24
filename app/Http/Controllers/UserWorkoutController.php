<?php

namespace App\Http\Controllers;

use App\Models\UserWorkout;
use Illuminate\Http\Request;

class UserWorkoutController extends Controller
{

    /////GET ALL USER WORKOUTS////////////////////////////////////////////////
    public function getalluserworkouts($id)
    {
        if (is_numeric($id)) {
            $data = UserWorkout::where('user_id', '=', $id)
                ->select('id', 'user_id', 'created_at')
                ->orderBy('created_at', 'DESC')
                ->get();
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 200);
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

    /////LOG USER WORKOUTS////////////////////////////////////////////////
    public function loguserworkout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'workout' => 'required|boolean'
        ]);

        if ($request) {
            $data = UserWorkout::create([
                'user_id' => $request->user_id,
                'workout' => $request->workout
            ]);
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
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


    /////DELETE WORKOUT////////////////////////////////////////////////
    public function deleteworkout(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        if ($request) {
            $data = UserWorkout::where('id', '=', $request->id)
                ->delete();
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ]);
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
}
