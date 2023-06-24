<?php

namespace App\Http\Controllers;

use App\Models\AppImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AppImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////GET ALL AIMAGE
    public function getallimages()
    {
        $all = AppImage::all();
        if ($all) {
            return response()->json($all, 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
    }



    /////ADD IMAGE
    public function addimages(Request $request)
    {

        $file_names = $_FILES["file"]["name"];
        $path = "storage/appimages/welcomeimages/";
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . "/storage/appimages/welcomeimages/";

        for ($i = 0; $i < count($file_names); $i++) {
            $file_name = $file_names[$i];
            $tmp = explode(".", $file_name);
            $extension = end($tmp);
            $original_file_name = pathinfo($file_name, PATHINFO_FILENAME);
            $newName = str_replace(' ', '_', $original_file_name) . '-' . rand() . '_' . time() . '.' . $extension;
            $size = $_FILES['file']['size'][$i];
            $user_id = $request->user_id;
            $daylog_id = $request->daylog_id;

            if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $folderPath . $newName)) {
                $image = new AppImage;
                $image->name = $newName;
                $image->path = $path . $newName;
                $image->size = $size;
                $image->type = $extension;

                if (!$image->save()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Bad query.'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Image too big.'
                ], 401);
            }
        }
        return response()->json(AppImage::all(), 201);
    }

    /////DELTE APP IMAGE
    public function deleteappimage(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'file_name' => 'required|string'
        ]);

        if ($request) {
            ///PRVO GA TREBA NACI U STORAGE
            $filename = $request->file_name;
            $exsist = Storage::exists('public/appimages/welcomeimages/' . $filename);
            if ($exsist) {
                $delete = Storage::delete('public/appimages/welcomeimages/' . $filename);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad request.'
                ], 403);
            }
            if ($delete) {
                $done = AppImage::where('id', '=', $request->id)->delete();
                if ($done) {
                    return response()->json(AppImage::all(), 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No such image'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad request.'
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }

}
