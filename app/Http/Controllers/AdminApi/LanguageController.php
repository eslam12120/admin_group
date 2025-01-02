<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $language = Language::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'Language created successfully!',
            'data' => $language
        ], 200);
    }

    // Read (All)
    public function index()
    {
        $languages = Language::all();
        return response()->json([
            'data' => $languages
        ]);
    }

    // Read (Single)
    public function show($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'message' => 'Language not found'
            ], 404);
        }

        return response()->json([
            'data' => $language
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
        ]);

        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'message' => 'Language not found'
            ], 404);
        }

        $language->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
        ]);

        return response()->json([
            'message' => 'Language updated successfully!',
            'data' => $language
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'message' => 'Language not found'
            ], 404);
        }

        $language->delete();

        return response()->json([
            'message' => 'Language deleted successfully!'
        ]);
    }
}
