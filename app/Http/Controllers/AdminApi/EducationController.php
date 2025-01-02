<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Education;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EducationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $education = Education::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'Education created successfully!',
            'data' => $education
        ], 200);
    }

    // Read (All)
    public function index()
    {
        $educations = Education::all();
        return response()->json([
            'data' => $educations
        ]);
    }

    // Read (Single)
    public function show($id)
    {
        $education = Education::find($id);

        if (!$education) {
            return response()->json([
                'message' => 'Education not found'
            ], 404);
        }

        return response()->json([
            'data' => $education
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $education = Education::find($id);

        if (!$education) {
            return response()->json([
                'message' => 'Education not found'
            ], 404);
        }

        $education->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'Education updated successfully!',
            'data' => $education
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $education = Education::find($id);

        if (!$education) {
            return response()->json([
                'message' => 'Education not found'
            ], 404);
        }

        $education->delete();

        return response()->json([
            'message' => 'Education deleted successfully!'
        ]);
    }
}
