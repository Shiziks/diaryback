<?php

namespace App\Http\Controllers;

use App\Models\image;
use App\Models\UserPhotos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class UserPhotosController extends Controller
{
    /////INSERT PHOTOS////////////////////////////////////////////////
    public function savephoto(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,png,jpeg,gif,svg,webp|max:5000|min:20',
            'user_id' => 'required|numeric|min:1',
            'profile' => 'required|numeric|min:1|max:1'
        ]);


        if ($request) {
            $user_id = $request->user_id;
            ///PRVO IZMENITI BAZU  DA SU SVE SLIKE U NJOJ NULA PROFIL
            //ako slike postoje u bazi
            $photosExist = UserPhotos::where('user_id', '=', $user_id)->get();
            if ($photosExist) {
                $changeProfile = UserPhotos::where('user_id', $user_id)->update(array('profile' => 0));
            }

            $file_name = $_FILES["file"]["name"];
            $tmp = explode(".", $file_name);
            $path = "storage/userphotos/";
            $folderPath = $_SERVER['DOCUMENT_ROOT'] . "/storage/userphotos/";
            $extension = end($tmp);

            $original_file_name = pathinfo($file_name, PATHINFO_FILENAME);

            $newName = str_replace(' ', '_', $original_file_name) . '-' . rand() . '_' . time() . '.' . $extension;

            $size = $_FILES['file']['size'];

            $profile = $request->profile;
            $tmpName = $_FILES["file"]["tmp_name"];

            $move = move_uploaded_file($tmpName, $folderPath . $newName);

            if ($move) {
                $userphoto = new UserPhotos;
                $userphoto->name = $newName;
                $userphoto->path = $path . $newName;
                $userphoto->size = $size;
                $userphoto->type = $extension;
                $userphoto->user_id = $user_id; //mora se poslati sa fronta uz request
                $userphoto->profile = $profile;


                if (!$userphoto->save()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Bad query.'
                    ], 401);
                } else {
                    return response()->json($userphoto, 201);
                }
            } else {
                return response()->json('mosa');
            }
        } else {
            return response()->json("Nista nije ok");
        }
    }


    /////GET USER PHOTOS////////////////////////////////////////////////
    public function getuserphotos($id)
    {
        if (!is_null($id)) {
            $data = UserPhotos::where('user_id', '=', $id)
                ->select(
                    'id AS id',
                    'user_id AS user_id',
                    'path AS file_path',
                    'type AS file_type',
                    'size AS file_size',
                    'name AS file_name',
                    'profile AS profile',
                    'created_at AS created_at'
                )
                ->get();
            if ($data) {
                return response()->json($data, 203)
                    ->header('Content-Type', 'multipart/form-data');
            } else {
                return response()->json([
                    'statis' => 'error',
                    'message' => "Bad query."
                ], 401);
            }
        }
    }

    /////GET IMAGES////////////////////////////////////////////////
    public function getImages($user_id)
    {
        if ($user_id > 0) {
            $img = UserPhotos::where('user_id', '=', $user_id)
                ->select(
                    'id AS id',
                    'user_id AS user_id',
                    'path AS file_path',
                    'type AS file_type',
                    'size AS file_size',
                    'name AS file_name',
                    'profile AS profile',
                    'created_at AS created_at'
                )
                ->get();
        }
        return $img;
    }


    /////DELETE USER PHOTO////////////////////////////////////////////////
    public function deletephoto(Request $request)
    {

        $request->validate([
            'id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1',
            'file_name' => 'required:string',
            'profile' => 'required|numeric|min:0|max:1'
        ]);

        if ($request) {
            $profile = $request->profile;
            $id = $request->id;
            $user_id = $request->user_id;
            $filename = $request->file_name;



            $exsist = Storage::exists('public/userphotos/' . $filename);
            if ($exsist) {
                $delete = Storage::delete('public/userphotos/' . $filename);
            }
            if ($delete) {
                $done = UserPhotos::where('id', '=', $id)->delete();

                if ($done) {
                    $allPhotos = $this->getImages($user_id);
                    //return response()->json(count($allPhotos), 200);
                    if ($profile == 1 && count($allPhotos) > 0) {
                        $updateProfile = UserPhotos::where('user_id', $user_id)
                            ->latest('created_at')
                            ->first()
                            ->update(array('profile' => 1));
                        $updated = $this->getImages($user_id);
                        return response()->json($updated, 200);
                    } else if ($profile == 0 && count($allPhotos) > 0) {
                        $updated = $this->getImages($user_id);
                        return response()->json($updated, 200);
                    } else {
                        $updated = $this->getImages($user_id); //bice prazno jer nema zapisa u bazi
                        return response()->json($updated, 200);
                    }
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not deleted'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bad request.'
                ], 400);
            }
        }
    }


    /////EDIT PROFILE PHOTO////////////////////////////////////////////////
    public function editprofilephoto(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1',
            'user_id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $id = $request->id;
            $user_id = $request->user_id;
            $done = UserPhotos::where('user_id', $user_id)->update(array('profile' => 0));
            if ($done) {
                $edited = UserPhotos::where('id', $id)->update(array('profile' => 1));
                if ($edited) {
                    $userPhotos = $this->getImages($user_id);
                    return response()->json([
                        'status' => 'success',
                        'data' => $userPhotos
                    ], 201);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Error editing profile column'
                    ], 400);
                }
            }
        }
    }
}
