<?php

namespace App\Http\Controllers;

use App\Models\daylog;
use Illuminate\Http\Request;
use App\Models\image;
use App\Http\Controllers\ImageController;

class DaylogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    ///GET ALL DAY LOGS OF A USER WITH ONE PIC
    public function getalluserdaylogs($id)
    {
        if ($id) {
            $allDayLogs = daylog::select(
                'daylogs.id as daylog_id',
                'daylogs.title',
                'daylogs.text',
                'daylogs.created_at',
                'images.file_name',
                'images.file_type',
                'images.file_size',
                'images.file_path'
            )
                ->leftJoin('images', 'daylogs.id', '=', 'images.daylog_id')
                ->where('daylogs.user_id', '=', $id)
                ->groupBy('daylogs.id')
                ->orderBy('daylogs.created_at', 'DESC')
                ->get();
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }
        if ($allDayLogs) {
            return response()->json([
                'status' => 'success',
                'data' => $allDayLogs
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No entries'
            ], 400);
        }
    }


    ///SAVE DAY LOG
    public function postdaylog(Request $request)
    {
        //VALIDIRATI REQUEST
        $request->validate([
            'user_id' => 'required|numeric|min:1',
            'title' => 'required|string|min:2|max:120',
            'text' => 'required|string|min:3|max:1500',
        ]);

        if ($request) {
            $daylog = daylog::create([
                'user_id' => $request->user_id,
                'text' => $request->text,
                'title' => $request->title
            ]);

            if ($daylog) {
                return response()->json([
                    'status' => 'success',
                    'data' => $daylog
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Daylog was not created'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }


    /////UPDATE DAY LOG
    public function updatedaylog(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
            'title' => 'string|required|min:2|max:120',
            'text' => 'string|required|min:3|max:1500',
            'daylog_id' => 'numeric|required|min:1'
        ]);

        if ($request) {
            $update = daylog::where('id', '=', $request->daylog_id)
                ->update(['title' => $request->title, 'text' => $request->text]);

            if ($update) {
                return response()->json([
                    'status' => 'success',
                    'data' => $update
                ], 201);
            } else return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        } else return response()->json([
            'status' => 'error',
            'message' => 'Bad request.'
        ], 400);
    }


    /////GET DAY LOG
    public function getdaylog($id)
    {
        if ($id > 0) {
            $dayloginfo = daylog::where('daylogs.id', '=', $id)
                ->select('id as daylog_id', 'title', 'text', 'user_id', 'created_at')
                ->first();
            if ($dayloginfo) {
                $fileinfo = image::where('images.daylog_id', '=', $id)->get();
            }
            if ($dayloginfo && $fileinfo) {
                $dayloginfo->{'images'} = $fileinfo;
                return response()->json(
                    $dayloginfo,
                    200
                );
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No such entry.'
                ], 400);
            }
        } else return response()->json([
            'status' => 'error',
            'message' => 'Bad request'
        ], 400);
    }


    ////// DELETE DAYLOG
    public function deletedaylog(Request $request)
    {
        $imageController = new ImageController();
        $request->validate([
            'daylog_id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $id = $request->daylog_id;
            $images = $request->images;
            if ($images) {
                for ($i = 0; $i < count($images); $i++) {
                    $send = new Request([
                        'id' => $images[$i]['id'],
                        'file_name' => $images[$i]['file_name']
                    ]);
                    $done = $imageController->deleteimage($send);
                    if (!$done) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Something went wrong'
                        ], 500);
                    }
                }
                $deleted = daylog::where('id', '=', $id)->delete();
                if ($deleted) {
                    return response()->json($deleted);
                } else return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong'
                ], 500);
            } else {
                $deleted = daylog::where('id', '=', $id)->delete();
                if ($deleted) {
                    return response()->json($deleted);
                } else return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }
}
