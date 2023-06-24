<?php

namespace App\Http\Controllers;

use App\Models\energy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnergyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /////EDIT ENERGY///////////////////////////////////////////////////////
    public function editenergy(Request $request)
    {
        $request->validate([
            'id' => '|numeric|min:1',
            'energy_name' => 'string|min:2|max:17',
        ]);

        $id = $request->id;
        $energy_name = $request->energy_name;
        $icons = $request->icons;
        if ($request) {
            if ($id && $energy_name) {
                $done = Energy::where('id', '=', $request->id)->update([
                    'energy_name' => $request->energy_name
                ]);
            }
            if ($icons) {
                $doneIcons = DB::transaction(function () use ($icons) {
                    foreach ($icons as $icon) {
                        $iconname = $icon['icon']['iconName'];
                        $iconPrefix = $icon['icon']['icon_prefix'];
                        Energy::where('id', '=', $icon['id'])->update(['icon' => $iconname, 'icon_prefix' => $iconPrefix]);
                    }
                });
            }
            return response()->json(Energy::all(), 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Bad request.'
            ], 403);
        }
    }

    ///// GET ALL ENERGY ENTRIES ///////////////////////////////////////////////////////
    public function getEnergyLevels()
    {
        $levels = Energy::all();
        if ($levels) {
            return response()->json([
                'status' => 'success',
                'data' => $levels
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Database error.'
            ], 401);
        }
    }
}
