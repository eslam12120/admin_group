<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $city = City::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'City created successfully!',
            'data' => $city
        ], 200);
    }

    // Read (All)
    public function index()
    {
        $cities = City::all();
        return response()->json([
            'data' => $cities
        ]);
    }

    // Read (Single)
    public function show($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'message' => 'City not found'
            ], 404);
        }

        return response()->json([
            'data' => $city
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'message' => 'City not found'
            ], 404);
        }

        $city->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'City updated successfully!',
            'data' => $city
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'message' => 'City not found'
            ], 404);
        }

        $city->delete();

        return response()->json([
            'message' => 'City deleted successfully!'
        ]);
    }
}
