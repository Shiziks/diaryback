<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMoodRequest;
use App\Http\Requests\UpdateMoodRequest;
use App\Models\Mood;
use Illuminate\Http\Request;


class MoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    //////GET MOODS////////////////////////////////////////////////
    public function getMoods()
    {
        $moods = Mood::all();
        if ($moods) {
            return response()->json([
                'status' => 'success',
                'data' => $moods
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 401);
        }
    }

    //////EDIT MOOD////////////////////////////////////////////////
    public function editmood(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'mood_name' => 'string|min:2|max:15',
            'icon' => 'string|min:5|max:30'
        ]);

        if ($request) {
            $mood_name = $request->mood_name;
            $icon = $request->icon;
            if ($mood_name && $icon) {
                $done = Mood::where('id', '=', $request->id)->update([
                    'mood_name' => $mood_name,
                    'icon' => $icon
                ]);
                if ($done) {
                    return response()->json(Mood::all(), 201);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error while updating.'
                    ], 500);
                }
            } else if ($mood_name) {
                $done = Mood::where('id', '=', $request->id)->update(['mood_name' => $request->mood_name]);
                if ($done) {
                    return response()->json(Mood::all(), 201);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error while updating.'
                    ], 500);
                }
            } else {
                $done = Mood::where('id', '=', $request->id)->update(['icon' => $request->icon]);
                if ($done) {
                    return response()->json(Mood::all(), 201);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error while updating.'
                    ], 500);
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }
}
