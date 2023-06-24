<?php

namespace App\Http\Controllers;

use App\Models\MoodUser;
use Illuminate\Http\Request;


class MoodUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    ////// GET ALL USER MOODS////////////////////////////////////////////////
    public function getAllUserMoods($user_id)
    {
        $allusermoods = MoodUser::where('user_id', '=', $user_id)
            ->join('moods', 'moods.id', '=', 'mood_users.mood_id')
            ->select('mood_users.user_id', 'mood_users.mood_id', 'moods.mood_name', 'mood_users.created_at')
            ->orderBy('mood_users.created_at', "DESC")
            ->get();
        if ($allusermoods) {
            return response()->json([
                'status' => 'success',
                'data' => $allusermoods
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad query.'
            ], 400);
        }
    }





    ///// SET NEW USER MOOD////////////////////////////////////////////////
    public function setUserMood(Request $request)
    {
        $request->validate([
            'mood_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);
        $usermood = MoodUser::create([
            'mood_id' => $request->mood_id,
            'user_id' => $request->user_id
        ]);


        if ($usermood) {
            return response()->json([
                'status' => 'success',
                'message' => 'Mood has been recorded',
                'id' => $usermood->id //vraca id unetog kako bi mogao da se update ukoliko user resi da promeni svoj mood tog dana
            ], 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 500);
        }
    }

    /////UPDATE USER MOODS////////////////////////////////////////////////
    public function UpdateUserMood(Request $request)
    {
        $id = $request->id;
        $done = MoodUser::where('id', '=', $id)->update(['mood_id' => $request->mood_id]);
        if ($done) {
            return response()->json([
                "status" => "success",
                'message' => $done
            ], 201);
        } else {
            return response()->json([
                "status" => "error",
                'message' => 'No such database entry'
            ], 400);
        }
    }

    ////GET CURRENT MOOD////////////////////////////////////////////////
    public function currentMood($user_id)
    {
        if ($user_id) {
            $date = date('Y-m-d');
            //dd($date);
            $currentMood = MoodUser::where("user_id", "=", $user_id)->where('created_at', 'LIKE', $date . '%')->latest()->first();
            if ($currentMood) {
                return response()->json(
                    $currentMood,
                    200
                );
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 500);
        }
    }
}
