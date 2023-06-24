<?php

namespace App\Http\Controllers;

use App\Models\Testemonial;
use App\Models\UserPhotos;
use Exception;
use Illuminate\Http\Request;

class TestemonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     
    /////CREATE TESTEMONIAL////////////////////////////////////////////////
    public function cratetestemonial(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:2|max:150',
            'title' => 'required|string|min:2|max:20',
            'user_id' => 'required|numeric|min:1',
            'anonymous' => 'min:0|max:1'
        ]);

        if ($request) {
            $done = Testemonial::create([
                'text' => $request->text,
                'title' => $request->title,
                'user_id' => $request->user_id,
                'anonymous' => $request->anonymous ? $request->anonymous : 0
            ]);
            if ($done) {
                $user = $this->gettestemonial($request->user_id);
                return response()->json($user->original, 201);
            } else {
                return response()->josn($this->responseToSend('error', 'Database error.'), 500);
            }
        } else {
            return response()->josn($this->responseToSend('error', 'Bad request.'), 403);
        }
    }


    /////GET TESTEMONIAL////////////////////////////////////////////////
    public function gettestemonial($id)
    {
        if ($id > 0) {
            try {
                $user = Testemonial::select("testemonials.user_id", "testemonials.text", "testemonials.title", 'testemonials.id', "testemonials.anonymous", "u.first_name", "u.last_name", "u.email")
                    ->join('users as u', 'u.id', '=', 'testemonials.user_id')
                    ->where('testemonials.user_id', '=', $id)
                    ->get()->first();

                $photo = UserPhotos::select('path')
                    ->where('user_id', '=', $id)
                    ->where('profile', '=', 1)
                    ->get()->first();

                if ($user) {
                    return response()->json(['user' => $user, 'photo' => $photo], 200);
                } else {
                    return response()->json($user, 200);
                }
            } catch (Exception $e) {
                return response()->json($this->responseToSend('error', 'Database error'), 500);
            }
        } else  return response()->json($this->responseToSend('error', 'Bad request'), 403);
    }


    /////GET ALL TESTEMONIALS////////////////////////////////////////////////
    public function getalltestemonials()
    {
        $t = Testemonial::select(
            'testemonials.id',
            'testemonials.user_id',
            'testemonials.text',
            'testemonials.title',
            'testemonials.anonymous',
            'users.first_name',
            'users.last_name',
            'users.first_name',
            'users.last_name',
            'user_photos.profile',
            'user_photos.path'
        )
            ->leftjoin('user_photos', 'testemonials.user_id', '=', 'user_photos.user_id')
            ->leftjoin('users', 'users.id', '=', 'testemonials.user_id')
            ->where('user_photos.profile', '=', null)
            ->get();

        $t1 = Testemonial::select(
            'testemonials.id',
            'testemonials.user_id',
            'testemonials.text',
            'testemonials.title',
            'testemonials.anonymous',
            'users.first_name',
            'users.last_name',
            'users.first_name',
            'users.last_name',
            'user_photos.profile',
            'user_photos.path'
        )
            ->leftjoin('user_photos', 'testemonials.user_id', '=', 'user_photos.user_id')
            ->leftjoin('users', 'users.id', '=', 'testemonials.user_id')
            ->where('user_photos.profile', '=', 1)
            ->get();

        $mrg = $t->merge($t1);
        if ($mrg) {
            return response()->json($mrg, 200);
        } else {
            $this->responseToSend('error', 'Database error', 500);
        }
    }


    //////DELETE TESTEMONIAL////////////////////////////////////////////////
    public function deletetestemonial(Request $request)
    {
        $request->validate([
            'id' => 'required|min:1|numeric'
        ]);

        if ($request) {
            $done = Testemonial::where('id', '=', $request->id)->delete();
            if ($done) {
                return response()->json($done, 201);
            } else {
                return response()->json($this->responseToSend('error', 'Database error'), 500);
            }
        } else {
            return response()->json($this->responseToSend('error', 'Bad request.'), 403);
        }
    }


    /////EDIT TESTEMONIAL////////////////////////////////////////////////
    public function edittestemonial(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'anonymous' => 'numeric|min:0|max:1',
            'text' => 'required|string|min:2|max:150',
            'title' => 'required|string|min:2|max:20',
            'user_id' => 'required|numeric|min:1',
        ]);

        if ($request) {
            if ($request->anonymous) {
                $anonymous = $request->anonymous;
            } else {
                $anonymous = 0;
            }
            $done = Testemonial::where('id', '=', $request->id)
                ->update(
                    [
                        'anonymous' => $anonymous,
                        'title' => $request->title,
                        'text' => $request->text,
                    ]
                );
            if ($done) {
                $user = $this->gettestemonial($request->user_id);
                return response()->json($user->original, 201);
            } else {
                return response()->josn($this->responseToSend('error', 'Database error.'), 500);
            }
        } else {
            return response()->josn($this->responseToSend('error', 'Bad request.'), 403);
        }
    }


    /////RESPONSE TO SEND////////////////////////////////////////////////
    public function responseToSend($status, $message)
    {
        $errorR = [
            'status' => $status,
            'message' => $message
        ];
        return $errorR;
    }
}
