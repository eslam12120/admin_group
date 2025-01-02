<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Government;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GovernmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $government = Government::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'Government created successfully!',
            'data' => $government
        ], 200);
    }

    // Read (All)
    public function index()
    {
        $governments = Government::all();
        return response()->json([
            'data' => $governments
        ]);
    }

    // Read (Single)
    public function show($id)
    {
        $government = Government::find($id);

        if (!$government) {
            return response()->json([
                'message' => 'Government not found'
            ], 404);
        }

        return response()->json([
            'data' => $government
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $government = Government::find($id);

        if (!$government) {
            return response()->json([
                'message' => 'Government not found'
            ], 404);
        }

        $government->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'Government updated successfully!',
            'data' => $government
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $government = Government::find($id);

        if (!$government) {
            return response()->json([
                'message' => 'Government not found'
            ], 404);
        }

        $government->delete();

        return response()->json([
            'message' => 'Government deleted successfully!'
        ]);
    }
}
