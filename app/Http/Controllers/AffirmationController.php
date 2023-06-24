<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\affirmation;

class AffirmationController extends Controller
{
    public function getallaffirmations()
    {
        // $affirmations=affirmation::pluck('affirmation');
        $affirmations = affirmation::all();
        if ($affirmations) {
            return response()->json($affirmations, 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error'
            ], 500);
        }
    }


    /////ADD AFFIRMATION
    public function addaffirmation(Request $request)
    {
        $request->validate([
            'affirmation' => 'required|string|min:2|max:20'
        ]);

        if ($request) {
            $done = affirmation::create(['affirmation' => $request->affirmation]);
            if ($done) {
                return response()->json(affirmation::all(), 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Database error."
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "Bad request."
            ], 403);
        }
    }


    /////EDIT AFFIRMATION
    public function editaffirmation(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'affirmation' => 'required|string|min:2|max:20'
        ]);

        if ($request) {
            $done = affirmation::where('id', '=', $request->id)
            ->update(['affirmation' => $request->affirmation]);
            if ($done) {
                return response()->json(affirmation::all(), 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Database error."
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "Bad request."
            ], 403);
        }
    }


    //////DELETE AFFIRMATION
    public function deleteaffirmation(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $done = affirmation::where('id', '=', $request->id)->delete();
            if ($done) {
                return response()->json(affirmation::all(), 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Database error."
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "Bad request."
            ], 403);
        }
    }
}
