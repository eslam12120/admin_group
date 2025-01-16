<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    /*
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'image' => 'nullable|string',
            'active' => 'nullable|integer',
        ]);
        if ($request->hasFile('image')) {
            $photo = $request->file('image');
            $ext = $photo->getClientOriginalName();
            $name = "ser-" . uniqid() . ".$ext";
            $photo->move(public_path('images/services'), $name);
            $imagePath = url("images/services/$name");
            $service = Service::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'image' => $imagePath,
                'active' => $request->active,
            ]);
        } else {
            $service = Service::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'active' => $request->active,
            ]);
        }


        return response()->json([
            'message' => 'Service created successfully!',
            'data' => $service
        ], 200);
    }
*/
    // Read (All)
    public function index()
    {
        $services = Service::whereIn('active', ['1', '0'])->get();
        return response()->json([
            'data' => $services
        ]);
    }

    // Read (Single)
    public function show($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'data' => $service
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'active' => 'nullable|integer',
        ]);

        // Find the service by ID
        $service = Service::where('id', $id)->first();

        if ($request->hasFile('image')) {
            $photo = $request->file('image');
            $ext = $photo->getClientOriginalExtension();
            $name = "ser-" . uniqid() . ".$ext";

            // Move the image to the services directory
            $photo->move(public_path('images/services'), $name);

            // Generate the full URL path
            $imagePath = url("images/services/$name");

            // Optionally delete the old image file (optional, ensure old path handling exists)
            if ($service->image && file_exists(public_path(parse_url($service->image, PHP_URL_PATH)))) {
                unlink(public_path(parse_url($service->image, PHP_URL_PATH)));
            }

            // Update the image path in the database
            $service->image = $imagePath;
        }

        // Update other fields
        $service->name_ar = $request->name_ar;
        $service->name_en = $request->name_en;
        $service->description_ar = $request->description_ar;
        $service->description_en = $request->description_en;
        $service->active = $request->active;

        // Save the changes
        $service->save();

        return response()->json([
            'message' => 'Service updated successfully!',
            'data' => $service,
        ], 200);
    }


    // Delete
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found'
            ], 404);
        }

        $service->update(['active' => '2']);

        return response()->json([
            'message' => 'Service deleted successfully!'
        ]);
    }
}
