<?php

namespace App\Http\Controllers;

use App\Models\ProfileCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////GET ALL CATEGORIES////////////////////////////////////////////////
    public function getprofilecategories()
    {
        $categories = ProfileCategory::all();
        $isAuth = Auth::check();

        if ($categories && $isAuth) {
            return response()->json([
                'status' => 'success',
                'check' => $isAuth,
                'data' => $categories
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorised user.'
            ], 401);
        }
    }

    /////CHANGE ADMIN CATEGORY STATUS////////////////////////////////////////////////
    public function changeadminstatus(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'admin_status' => 'required|min:0|max:1'
        ]);

        if ($request) {
            $done = ProfileCategory::where('id', '=', $request->id)->update(['admin_status' => $request->admin_status]);
            if ($done) {
                return response()->json(ProfileCategory::all(), 200);
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
            ], 403);
        }
    }
}
