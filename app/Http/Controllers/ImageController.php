<?php

namespace App\Http\Controllers;

use App\Models\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////GET ALL POST IMAGES////////////////////////////////////////////////
    public function getallpostimages($daylog_id)
    {
        $images = image::where('daylog_id', '=', $daylog_id)->get();
        if ($images) {
            return response()->json($images, 200)
                ->header('Content-Type', 'multipart/form-data');
        } else {
            return response()->json([
                'statis' => 'error',
                'message' => "Bad query."
            ], 401);
        }
    }


    /////POST IMAGES////////////////////////////////////////////////
    public function postimage(Request $request)
    {

        $file_names = $_FILES["file"]["name"];
        $path = "storage/images/";
        $folderPath = $_SERVER['DOCUMENT_ROOT'] . "/storage/images/";

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
                $image = new Image;
                $image->file_name = $newName;
                $image->file_path = $path . $newName;
                $image->file_size = $size;
                $image->file_type = $extension;
                $image->user_id = $user_id; //mora se poslati sa fronta uz request
                $image->daylog_id = $daylog_id; //mora se poslati sa fronta uz request

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
        return response()->json([
            'status' => 'success',
            'message' => 'Images uploaded.',

        ], 201);
    }


    /////DELETE IMAGE////////////////////////////////////////////////
    public function deleteimage(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'file_name' => 'required|string'
        ]);
        //prvo naci fajl u storage


        // $exist=Storage::exists('public/images/'.$filename);
        // $delete=Storage::delete('public/images/10-2500x1667-77111856_1665140370.jpg');
        // dd($delete);
        ///Users/shiziks/Desktop/Angular/diaryapp/diaryback/app
        ////Users/shiziks/Desktop/Angular/diaryapp/diaryback/storage
        //10-2500x1667-77111856_1665140370.jpg
        //300572_1280-436494077_1665684097.jpg
        ////Users/shiziks/Desktop/Angular/diaryapp/diaryback/storage/app/public/images/300572_1280-436494077_1665684097.jpg



        if ($request) {
            $filename = $request->file_name;
            $exsist = Storage::exists('public/images/' . $filename);
            if ($exsist) {
                $delete = Storage::delete('public/images/' . $filename);
            }
            if ($delete) {
                $done = image::where('id', '=', $request->id)->delete();
                if ($done) {
                    return response()->json([
                        'status' => 'success',
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Bad request'
                    ], 400);
                }
            }
        }
    }
}
