<?php

namespace App\Http\Controllers;

use App\Models\ProfileCategory;
use App\Models\ProfileSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfileSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////ADD NEW SUBCATEGORY////////////////////////////////////////////////
    public function addNewSubcategory(Request $request)
    {
        $request->validate([
            'subcategory_name' => 'required|string|min:2',
            'profilecategory_id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $done = ProfileSubcategory::create([
                'subcategory_name' => $request->subcategory_name,
                'profilecategory_id' => $request->profilecategory_id,
                'admin_status' => 1
            ]);
            if ($done) {
                return response()->json([
                    'status' => 'success',
                    'data' => $done
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Databse error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }

    /////DELETE SUBCATEGORY////////////////////////////////////////////////
    public function deletesubcategory(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $done = ProfileSubcategory::where('id', '=', $request->id)->delete();
            if ($done) {
                return response()->json([
                    'status' => 'success',
                    'data' => $done
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Databse error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }

    /////GET ALL SUBCATEGORIES////////////////////////////////////////////////
    public function getallsubcategories()
    {
        $done = ProfileCategory::all();
        if ($done) {
            return response()->json([
                'status' => 'success',
                'data' => $done
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Databse error.'
            ], 500);
        }
    }

    /////GET CATEGORY SUBCATEGORIES////////////////////////////////////////////////
    public function getcategorysubcategories($category)
    {
        if (is_numeric($category)) {
            if ($category > 0) {
                $done = ProfileSubcategory::where('profilecategory_id', '=', $category)->get();
                if ($done) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $done
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Databse error.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad request.'
                ], 403);
            }
        } else {
            $done = ProfileCategory::where('name', '=', $category)->join('profile_subcategories', 'profile_categories.id', '=', 'profile_subcategories.profilecategory_id')
                ->select('profile_subcategories.id', 'profile_subcategories.subcategory_name', 'profile_subcategories.profilecategory_id', 'profile_subcategories.admin_status')
                ->get();
            if ($done) {
                return response()->json($done, 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Databse error.'
                ], 500);
            }
        }
    }

    /////CHANGE SUBCATEGORY STATUS////////////////////////////////////////////////
    public function changesubcategorystatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'data1' => 'id|status',
            'data2' => 'id|status'
        ]);
        if ($validator) {
            $res = DB::transaction(function () use ($request) {
                if ($request->data1) {
                    $change1 = $request->data1;
                    ProfileSubcategory::where('id', '=', $change1['id'])->update(['admin_status' => $change1['status']]);
                }
                if ($request->data2) {
                    $change2 = $request->data2;
                    ProfileSubcategory::where('id', '=', $change2['id'])->update(['admin_status' => $change2['status']]);
                }
            });
            return response()->json($res, 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }
}
