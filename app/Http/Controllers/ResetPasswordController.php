<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\resetpassword;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['sendemail', 'resetpassword']]);
    }

    /////SEND RESET EMAIL////////////////////////////////////////////////
    public function sendemail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);

        if ($request) {
            $email = $request->email;
            $token = $this->createPasswordToken($email);
            if ($token !== false) {

                Mail::to($email)->send(new ResetPasswordEmail($token, $email));
                return response()->json([
                    'status' => 'success',
                    'data' => 'Email has been sent to: ' . $email
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No account found with this email.'
                ], 404);
            }
        }
    }


    /////RESET PASSWORD////////////////////////////////////////////////
    public function resetpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string|min:6', //dodati min i max i pattern
            'password_token' => 'required|string', //dodati min i max
            'password' => 'required|string|min:8' //dodati min i max i pattern
        ]);
        if ($request) {
            $email = $request->email;
            $newPassword = $request->password;
            $password_token = $request->password_token;
            $tmp = $this->checkEmailAndToken($email, $password_token);
            if ($tmp != null) {
                $id = $tmp->user_id;
                $cryptedPassword = bcrypt($newPassword);
                $user = User::find($id);
                $user->update(['password' => $cryptedPassword]);
                $removeToken = resetpassword::where('password_token', '=', $password_token)->delete();
                if ($user) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $user
                    ], 201);
                } else return response()->json([
                    'status' => 'error',
                    'message' => 'Update error'
                ], 500);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token.'
                ], 404);
            }
        }
    }

    /////CHECK EMAIL AND TOKEN////////////////////////////////////////////////
    private function checkEmailAndToken($email, $password_token)
    {
        $exists = resetpassword::where([['email', '=', $email], ['password_token', '=', $password_token]])->get()->first();
        $created = strtotime($exists->created_at) + 150;
        $now = strtotime(date('y-m-d h:i:s'));
        if ($now <= $created) {
            return $exists;
        } else return null;
    }


    /////SAVE TOKEN////////////////////////////////////////////////
    public function saveToken($token, $user_id, $email)
    {
        $created = resetpassword::create([
            'user_id' => $user_id,
            'password_token' => $token,
            'email' => $email
        ]);
        return response()->json($created);
    }

    /////EMAIL EXISTS////////////////////////////////////////////////
    public function emailExists($email)
    {
        return User::where('email', '=', $email)->first();
        ///vraca samo true ili false ako se ispred User dodaju dva uzvicnika

    }


    /////CREATE PASSWORD TOKEN////////////////////////////////////////////////
    private function createPasswordToken($email)
    {
        $user = $this->emailExists($email);
        if ($user !== null) {
            $oldToken = $this->tokenExists($user->id);
            if ($oldToken) {
                $token = $oldToken;
            } else {
                $token = Str::random(64);
                $saved = $this->saveToken($token, $user->id, $email);
            }
            return $token;
        } else {
            return false;
        }

    }


    /////TOKEN EXISTS////////////////////////////////////////////////
    private function tokenExists($id)
    {
        $tmp = resetpassword::where('user_id', '=', $id)->orderBy('created_at', 'desc')->first();
        if ($tmp) {
            $now = strtotime(date('y-m-d h:i:s'));
            $time = strtotime($tmp->created_at) + 150;
            if ($now >= $time) {
                return $tmp->password_token;
            } else return null;
        } else {
            return null;
        }
    }
}
