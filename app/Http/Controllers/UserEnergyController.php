<?php

namespace App\Http\Controllers;

use App\Models\UserEnergy;
use Illuminate\Http\Request;

class UserEnergyController extends Controller
{

    /////GET USER ENERGY////////////////////////////////////////////////
    public function getUserEnergy($id)
    {
        $userEnergy = UserEnergy::where('user_id', '=', $id)
            ->join('energies', 'energies.id', '=', 'user_energies.energy_id')
            ->select('user_energies.user_id', 'energies.id as energy_id', 'energies.energy_name', 'user_energies.created_at')
            ->orderBy('user_energies.created_at', "DESC")
            ->get();

        if ($userEnergy) {
            return response()->json([
                'status' => 'success',
                'data' => $userEnergy
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 500);
        }
    }


    /////SET USER ENERGY////////////////////////////////////////////////
    public function setUserEnergy(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'energy_id' => 'required'
        ]);

        $userEnergy = UserEnergy::create([
            'user_id' => $request->user_id,
            'energy_id' => $request->energy_id
        ]);

        if ($userEnergy) {
            return response()->json([
                'status' => 'success',
                'userenergy_id' => $userEnergy->id
            ], 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 400);
        }
    }


    /////UPDATE USER ENERGY////////////////////////////////////////////////
    public function updateUserEnergy(Request $request)
    {
        $id = $request->id;
        $done = UserEnergy::where('id', '=', $id)->update(['energy_id' => $request->energy_id]);
        if ($done) {
            return response()->json([
                "status" => "success",
                'message' => $done
            ], 201);
        } else {
            return response()->json([
                "status" => "error",
                'message' => 'Bad request.'
            ], 404);
        }
    }

    /////GET LAST UPDATED USER ENERGY////////////////////////////////////////////////
    public function getCurrentUserEnergy($user_id)
    {
        if ($user_id) {
            $date = date('Y-m-d');
            $currentUserEnergy = UserEnergy::where("user_id", "=", $user_id)->where('created_at', 'LIKE', $date . '%')->latest()->first();
            if ($currentUserEnergy) {
                return response()->json($currentUserEnergy, 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request'
            ], 400);
        }
    }
}
