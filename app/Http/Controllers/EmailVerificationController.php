<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Models\User;

class EmailVerificationController extends Controller
{


    public function __construct()
    {
        //$this->middleware('auth:api')->only('resend');
        //$this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        //user can only make 6 requests in 1 minute


    }
    public function verify(Request $request)
    {

        //return response($request);
        if (!$request->hasValidSignature()) {
            //$this->respondUnAuthorizedRequest(253);
            $url = 'http://localhost:4200/emailverificationerror/' . $request->email;
            return redirect($url)->with(['status', 'Bad request.']);
            //preusmeriti na stranicu koja je mistake
        }

        $user = User::where('id', '=', $request->route('id'))->get()->first();
        if ($user) {
            if ($request->route('id') != $user->id) {
                return redirect('http://localhost:4200/mistake')->with('status', 'Bad request.');

                //throw new AuthorizationException;
                //ako id nije dobar sta uraditi
            }

            if ($user->hasVerifiedEmail()) {
                //response(['message'=>'Already verified.']);
                return redirect('http://localhost:4200/emailverified')->with('status', 'Already verified.');
            }

            $done = $user->markEmailAsVerified();
            if ($done) {
                event(new Verified($user));
                //response(['message'=>'Successfully verified.']);
                return redirect('http://localhost:4200/emailverified')->with('status', 'Email verified.');
            }
        } else {
            ////REDIREKTOVATI NA STRANICU gde kaze da je istekao token i da pokusa ponovo
            return redirect('http://localhost:4200/mistake')->with('status', 'Token expired.');

            // return response()->json([
            //     'status'=>'error',
            //     'message'=>'Email or password are incorrect.'], 403);
        }
    }


    public function resend($email, Request $request)
    {
        if ($email == '') {
            return response(['message' => "Bad request."]);
        }
        $user = User::where('email', '=', $email)->get()->first();
        if ($user) {
            if ($user->hasVerifiedEmail()) {
                return response(['message' => "Email already verified"]);
            }
            $user->sendEmailVerificationNotification();
            return response(['message' => 'Verification email sent.']);
        } else {

            return response()->json([
                'status' => 'error',
                'message' => 'Email or password are incorrect.'
            ], 403);
        }
    }
}
