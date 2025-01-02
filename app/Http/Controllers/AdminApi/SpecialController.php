<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Special;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecialController extends Controller
{
    public function index()
    {
        $specials = Special::whereIn('active', ['1', '0'])->get();
        return response()->json([
            'message' => 'Special created successfully!',
            'data' =>  $specials,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'job_name_en' => 'nullable|string|max:255',
            'job_name_ar' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $photo = $request->file('image');
            $ext = $photo->getClientOriginalName();
            $name = "special-" . uniqid() . ".$ext";
            $photo->move(public_path('special_images'), $name);
            $imagePath = url("special_images/$name");
        }

        $special = Special::create(array_merge(
            $request->only([
                'name_en',
                'name_ar',
                'description_en',
                'description_ar',
                'job_name_en',
                'job_name_ar',
                'active',
            ]),
            ['image' => $imagePath]
        ));

        return response()->json([
            'message' => 'Special created successfully!',
            'data' => $special,
        ], 200);
    }

    public function show($id)
    {
        $special = Special::find($id);

        if (!$special) {
            return response()->json(['message' => 'Special not found'], 404);
        }

        return response()->json([
            'message' => 'Special show successfully!',
            'data' =>  $special,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $special = Special::find($id);

        if (!$special) {
            return response()->json(['message' => 'Special not found'], 404);
        }

        $request->validate([
            'name_en' => 'nullable|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'job_name_en' => 'nullable|string|max:255',
            'job_name_ar' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'nullable|integer',
        ]);

        $imagePath = $special->image;
        if ($request->hasFile('image')) {
            $photo = $request->file('image');
            $ext = $photo->getClientOriginalName();
            $name = "special-" . uniqid() . ".$ext";
            $photo->move(public_path('special_images'), $name);
            $imagePath = url("special_images/$name");
        }

        $special->update(array_merge(
            $request->only([
                'name_en',
                'name_ar',
                'description_en',
                'description_ar',
                'job_name_en',
                'job_name_ar',
                'active',
            ]),
            ['image' => $imagePath]
        ));

        return response()->json([
            'message' => 'Special updated successfully!',
            'data' => $special,
        ], 200);
    }

    public function destroy($id)
    {
        $special = Special::find($id);

        if (!$special) {
            return response()->json(['message' => 'Special not found'], 404);
        }

        $special->update(['active' => '2']);

        return response()->json(['message' => 'Special deleted successfully!'], 200);
    }
}
