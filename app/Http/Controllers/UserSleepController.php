<?php

namespace App\Http\Controllers;

use App\Models\sleepHours;
use App\Models\UserSleep;
use Illuminate\Http\Request;

class UserSleepController extends Controller
{

    /////ALL SLEEP HOURS OF A USER////////////////////////////////////////////////
    public function getallusersleepinghours($id)
    {
        if ($id > 0) {
            $allTimes = UserSleep::select('user_id', 'sleep_hours_id', 'sleep_hours.hour', 'sleep_hours.hours as sleep_hours', 'user_sleeps.created_at')->JOIN('sleep_hours', 'sleep_hours_id', '=', 'sleep_hours.id')->where('user_id', '=', $id)->orderBy('user_sleeps.created_at', 'DESC')
                ->get();

            if ($allTimes) {
                return response()->json([
                    'status' => 'success',
                    'data' => $allTimes
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No such entry.'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }


    /////GET CURRENT SLEEP HOURS////////////////////////////////////////////////
    public function getUserCurrentSleepingHours($id)
    {
        if ($id > 0) {
            $currentSleepingTime = UserSleep::select('user_sleeps.id', 'user_id', 'sleep_hours.hour', 'sleep_hours.hours as sleep_hours', 'user_sleeps.created_at')->JOIN('sleep_hours', 'sleep_hours_id', '=', 'sleep_hours.id')
                ->where('user_id', '=', $id)
                ->latest()
                ->first();
            return response()->json([
                'status' => 'success',
                'data' => $currentSleepingTime
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad query.',
            ], 400);
        }
    }


    /////SET USER SLEEPING HOURS////////////////////////////////////////////////
    public function setUserSleepingHours(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'sleep_hours' => 'required|numeric|min:1|max:12'
        ]);

        if ($request) {
            $sleepHoursId = sleepHours::where('hours', '=', $request->sleep_hours)->first();
            if ($sleepHoursId) {
                $userSleep = UserSleep::create([
                    'user_id' => $request->user_id,
                    'sleep_hours_id' => $sleepHoursId->id
                ]);
                if ($userSleep) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $userSleep
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


    /////UPDATE USER SLEEP HOURS////////////////////////////////////////////////
    public function updateSleepHours(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'sleep_hours' => 'required|numeric|min:1|max:12'
        ]);
        if ($request) {
            $sleepHoursId = sleepHours::where('hours', '=', $request->sleep_hours)->first();
            $updated = UserSleep::where('id', '=', $request->id)->update(['sleep_hours_id' => $sleepHoursId->id]);
            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'data' => $updated
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad query.'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }
}
