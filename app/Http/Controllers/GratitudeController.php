<?php

namespace App\Http\Controllers;


use App\Models\Gratitude;
use App\Models\gratitudegroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GratitudeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////ALL USER GRATITUDES/////////////////////////////////////////////////////////
    public function allusergratitudes($id)
    {
        if ($id && $id > 0) {
            $allgratitudes = Gratitude::where('user_id', '=', $id)->get();
            if ($allgratitudes) {
                return response()->json([
                    'status' => 'success',
                    'data' => $allgratitudes
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'messsage' => 'Invalid query'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request parameters.'
            ], 400);
        }
    }


    /////GET USER GRATITUDES ORGANIZED BY GROUP_ID///////////////////////////////////////
    public function getgratitudesbygroup($id)
    {
        if ($id > 0) {
            $gratitudes = Gratitude::select(
                'group_id',
                'created_at',
                DB::raw("(GROUP_CONCAT(gratitudes SEPARATOR '^ ')) as gratitudes")
            )
                ->where('user_id', '=', $id)
                ->groupBy('group_id')
                ->orderBy('created_at', 'DESC')
                ->get();

            if ($gratitudes) {
                return response()->json(
                    $gratitudes,
                    200
                );
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No such entries.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }


        //SELECT group_id, created_at, GROUP_CONCAT(gratitudes SEPARATOR'& ') FROM `gratitudes` WHERE user_id=2 GROUP by group_id
    }



    /////SET USER GRATITUDES//////////////////////////////////////////////////////
    public function setusergratitudes(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
            'gratitude1' => 'string|nullable|min:3|max:200',
            'gratitude2' => 'string|nullable|min:3|max:200',
            'gratitude3' => 'string|nullable|min:3|max:200',
        ]);

        if ($request) {

            $group_id = DB::transaction(function () use ($request) {

                $group = gratitudegroup::create();
                if ($group) {
                    $group_id = $group->id;
                }

                $user_id = $request->user_id;
                $gratitude1 = $request->gratitude1;
                $gratitude2 = $request->gratitude2;
                $gratitude3 = $request->gratitude3;

                if (isset($gratitude1)) {
                    $result1 = Gratitude::create([
                        'user_id' => $user_id,
                        'gratitudes' => $gratitude1,
                        'group_id' => $group_id
                    ]);
                    $id1 = $result1->id;
                }

                if ($gratitude2) {
                    $result2 = Gratitude::create([
                        'user_id' => $user_id,
                        'gratitudes' => $gratitude2,
                        'group_id' => $group_id
                    ]);
                    $id2 = $result2->id;
                }

                if ($gratitude3) {
                    $result3 = Gratitude::create([
                        'user_id' => $user_id,
                        'gratitudes' => $gratitude3,
                        'group_id' => $group_id
                    ]);
                    $id3 = $result3->id;
                }

                $group_ids = [];
                $group_ids += ['group_id' => $group_id];
                if (isset($id1) && !is_null($id1)) {
                    $group_ids += ['gratitude1_id' => $id1];
                }
                if (isset($id2) && !is_null($id2)) {
                    $group_ids += ['gratitude2_id' => $id2,];
                }
                if (isset($id3) && !is_null($id3)) {
                    $group_ids += ['gratitude3_id' => $id3];
                }

                return $group_ids;
            });
            if ($group_id) {
                return response()->json([
                    'status' => 'success',
                    'data' => $group_id
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad request.'
                ], 400);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }


    /////DELETE GRATITUDE/////////////////////////////////////////////////////////
    public function deleteGratitude($gratitude_id)
    {
        $done = Gratitude::where('id', '=', $gratitude_id)->delete();
        return $done;
    }


    /////UPDATE USER GRATITUDES/////////////////////////////////////////////////////////
    public function updategratitudes(Request $request)
    {

        $request->validate([
            'user_id' => 'numeric|min:1|required',
            'group_id' => 'numeric|min:1|required',
            'gratitude1_id' => 'numeric|nullable|min:1',
            'gratitude2_id' => 'numeric|nullable|min:1',
            'gratitude3_id' => 'numeric|nullable|min:1',
            'gratitude1' => 'string|nullable|min:3|max:200',
            'gratitude2' => 'string|nullable|min:3|max:200',
            'gratitude3' => 'string|nullable|min:3|max:200',
        ]);

        if ($request) {
            $user_id = $request->user_id;

            $gratitude1_id = $request->gratitude1_id;
            $gratitude2_id = $request->gratitude2_id;
            $gratitude3_id = $request->gratitude3_id;

            $gratitude1 = $request->gratitude1;
            $gratitude2 = $request->gratitude2;
            $gratitude3 = $request->gratitude3;

            $group_id = $request->group_id;

            if (!is_null($gratitude1_id) && !is_null($gratitude1)) {
                $upgr1 = Gratitude::where('id', '=', $gratitude1_id)
                    ->update(['gratitudes' => $gratitude1]);
            } else if (is_null($gratitude1_id) && !is_null($gratitude1)) {
                $setgr1 = Gratitude::create([
                    'user_id' => $user_id,
                    'group_id' => $group_id,
                    'gratitudes' => $gratitude1
                ]);
            } else if (!is_null($gratitude1_id) && is_null($gratitude1)) {
                $deletegr1 = GratitudeController::deleteGratitude($gratitude1_id);
            }

            if (!is_null($gratitude2_id) && !is_null($gratitude2)) {
                $upgr2 = Gratitude::where('id', '=', $gratitude2_id)
                    ->update(['gratitudes' => $gratitude2]);
            } else if (is_null($gratitude2_id) && !is_null($gratitude2)) {
                $setgr2 = Gratitude::create([
                    'user_id' => $user_id,
                    'group_id' => $group_id,
                    'gratitudes' => $gratitude2
                ]);
            } else if (!is_null($gratitude2_id) && is_null($gratitude2)) {
                $deletegr2 = GratitudeController::deleteGratitude($gratitude2_id);
            }

            if (!is_null($gratitude3_id) && !is_null($gratitude3)) {
                $upgr3 = Gratitude::where('id', '=', $gratitude3_id)
                    ->update(['gratitudes' => $gratitude3]);
            } else if (is_null($gratitude3_id) && !is_null($gratitude3)) {
                $setgr3 = Gratitude::create([
                    'user_id' => $user_id,
                    'group_id' => $group_id,
                    'gratitudes' => $gratitude3
                ]);
            } else if (!is_null($gratitude3_id) && is_null($gratitude3)) {
                $deletegr3 = GratitudeController::deleteGratitude($gratitude3_id);
            }



            $data = ['status' => 'success'];
            if (isset($upgr1) || isset($upgr2) || isset($upgr3)) {
                $data += ['data' => '1'];
            }
            if (isset($setgr1)) {
                $data += ['gratitude1_id' => $setgr1->id];
            }
            if (isset($setgr2)) {
                $data += ['gratitude2_id' => $setgr2->id];
            }
            if (isset($setgr3)) {
                $data += ['gratitude3_id' => $setgr3->id];
            }
        }

        if ($data) {
            return response()->json($data, 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }


    /////GET CURRENT USER GRATITUDES/////////////////////////////////////////////
    public function getcurrentusergratitudes(Request $request)
    {
        $gratitude_info = [];
        $currentDate = date('Y-m-d') . ' 00:00:00';
        $gratitudes = Gratitude::where('user_id', '=', $request->user_id)
            ->where('updated_at', '>=', $currentDate)->get();
        if (count($gratitudes) > 0) {
            $gratitude_info += [
                'group_id' => $gratitudes[0]->group_id,
                'user_id' => $gratitudes[0]->user_id
            ];
        }
        foreach ($gratitudes as $key => $gratitude) {
            $gratitude_info += [
                'gratitude_id' . $key + 1 => $gratitude->id,
                'gratitude' . $key + 1 => $gratitude->gratitudes
            ];
        }
        if ($gratitudes) {
            return response()->json([
                'status' => 'success',
                'data' => $gratitude_info
            ]);
        }
    }


    /////DELETE GRATITUDES////////////////////////////////////////////////////
    public function deletegratitudes(Request $request)
    {
        $request->validate([
            'group_id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1'
        ]);
        if ($request) {
            $deletedGratitudes = Gratitude::where('user_id', '=', $request->user_id)->where('group_id', '=', $request->group_id)->delete();
            if ($deletedGratitudes) {
                $deleteGroup = gratitudegroup::where('id', '=', $request->group_id)->delete();
                return response()->json($deleteGroup, 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No such entry.'
                ], 403);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }
}
