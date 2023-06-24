<?php

namespace App\Http\Controllers;

use App\Models\sleepHours;
use Illuminate\Http\Request;

class SleepHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////GET ALL SLEEP HOURS////////////////////////////////////////////////
    public function getsleephours()
    {
        $done = sleepHours::all();
        if ($done) {
            return response()->json($done, 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 500);
        }
    }


    ////EDIT SLEEP HOUR////////////////////////////////////////////////
    public function editsleephour(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'hour' => 'required|string|min:2'
        ]);
        if ($request) {
            $done = sleepHours::where('id', '=', $request->id)->update(['hour' => $request->hour]);
            if ($done) {
                return response()->json(sleepHours::all(), 201);
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
            ]);
        }
    }
}
