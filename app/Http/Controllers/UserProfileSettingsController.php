<?php

namespace App\Http\Controllers;

use App\Models\UserProfileSettings;
use Illuminate\Http\Request;
use App\Models\ProfileCategory;

class UserProfileSettingsController extends Controller
{

    /////GET USER PROFILE SETTINGS////////////////////////////////////////////////
    public function getuserprofilesettings($user_id)
    {
        if ($user_id > 0) {
            $settings = UserProfileSettings::where([['user_id', '=', $user_id], ['profile_categories.admin_status', '=', 1]])
                ->join('profile_categories', 'user_profile_settings.category_id', '=', 'profile_categories.id')
                ->select('user_profile_settings.id', 'user_profile_settings.user_id', 'user_profile_settings.category_id', 'user_profile_settings.status', 'profile_categories.name', 'profile_categories.admin_status')
                ->get();
            if ($settings) {
                return response()->json([
                    'status' => 'success',
                    'data' => $settings
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad request.'
                ], 400);
            }
        }
    }

    /////EDIT USER PROFILE SETTINGS////////////////////////////////////////////////
    public function edituserprofilesettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:4|max:15',
            'user_id' => 'required|numeric|min:1',
            'status' => 'numeric|min:0|max:1'
        ]);

        if ($request) {
            $status = $request->status ? (int)$request->status : 0;
            $name = $request->name;
            $user_id = $request->user_id;
            $category_id = ProfileCategory::where('name', '=', $name)->select('id')->first();
            if ($category_id->id > 0) {
                $number = $category_id->id;
                $done = UserProfileSettings::where([['category_id', '=', $number], ['user_id', '=', $user_id]])->update(array('status' => $status));
            }
            if ($done) {
                return response()->json($done);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad query.'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad query.'
            ], 400);
        }
    }
}
