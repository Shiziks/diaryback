<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;



class QuoteController extends Controller
{
    /////GET QUOTE////////////////////////////////////////////////
    public function getQuote(Request $request)
    {
        $isAuth = FacadesJWTAuth::user();
        if ($isAuth) {
            $quotes = Quote::all();
            $number = count($quotes);
            return response()->json([
                'status' => 'success',
                'user' => $isAuth,
                'number' => $quotes[rand(0, ($number - 1))]
            ], 200);
        } else {
        }
    }

    /////GET ALL QUOTES////////////////////////////////////////////////
    public function allQuotes()
    {
        $isAuth = FacadesJWTAuth::user();
        if ($isAuth) {
            $quotes = Quote::orderBy('created_at', 'DESC')->get();
            if ($quotes) {
                return response()->json(
                    $quotes,
                    200
                );
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorised user.'
            ], 401);
        }
    }

    /////DELETE QUOTE////////////////////////////////////////////////
    public function deletequote(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $done = Quote::where('id', '=', $request->id)->delete();
            if ($done) {
                return response()->json(Quote::orderBy('created_at', 'DESC')->get(), 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 403);
        }
    }




    /////ADD QUOTE////////////////////////////////////////////////
    public function addquote(Request $request)
    {
        $request->validate([
            'quote_text' => 'required|string|min:5'
        ]);
        $text = $request->quote_text;
        if ($request->quote_author) {
            $author = $request->quote_author;
        } else {
            $author = "Unknown Author";
        }
        if ($request) {
            $added = Quote::create([
                'quote_text' => $text,
                'quote_author' => $author
            ]);
            if ($added) {
                return response()->json(Quote::orderBy('created_at', 'DESC')->get(), 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 403);
        }
    }


    /////EDIT QUOTE////////////////////////////////////////////////
    public function editquote(Request $request)
    {
        $request->validate([
            'quote_text' => 'required|string|min:5|max:100',
            'quote_author' => 'min:0,max:30',
            'id' => 'required|numeric|min:1'
        ]);

        if ($request) {
            $text = $request->quote_text;
            $author = $request->quote_author ? $request->quote_author : "Unknown Author";
            $id = $request->id;

            $done = Quote::where('id', '=', $id)->update(array('quote_text' => $text, 'quote_author' => $author));

            if ($done) {
                return response()->json([
                    'status' => 'success',
                    'data' => Quote::orderBy('created_at', "DESC")->get()
                ], 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Update error.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 403);
        }
    }
}
