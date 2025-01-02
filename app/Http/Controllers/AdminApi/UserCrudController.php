<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserCrudController extends Controller
{
    //
    public function add_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'name' => 'required',
            'phone' => 'required|unique:users,phone',
        ], [
            'email.required' => trans('auth.email.register'),
            'password.required' => trans('auth.password.register'),
            'password.min' => trans('auth.password.min.register'),
            'password.max' => trans('auth.password.max.register'),

        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {

            $user = User::create([

                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'name' => $request->name,
                'sign_in_type' => $request->sign_in_type,
            ]);

            DB::commit();

            return response()->json([

                'status' => '200',
                'message' => trans('Added'),

            ]);
        } catch (Exception $e) {
            // Rollback all operations if an error occurs
            DB::rollBack();

            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 400);
        }
    }
    public function update_user(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:users,email,' . $request->id,

            'name' => 'nullable|string|max:255',
           
        ], [
            'email.email' => trans('auth.email.invalid'),
            'password.confirmed' => trans('auth.password.mismatch'),
            'password.min' => trans('auth.password.min.update'),
            'password.max' => trans('auth.password.max.update'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->id);
            $name = $user->image;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $ext = $photo->getClientOriginalName();
                $name = "user-" . uniqid() . ".$ext";
                $photo->move(public_path('images/users'), $name);
            }


            // Update user attributes
            $user->update([
                'email' => $request->email ?? $user->email,
                'name' => $request->name ?? $user->name,
                'phone' => $request->phone ?? $user->phone,
                'image' => $name,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => trans('auth.user.updated'),
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Failed to update user: ' . $e->getMessage()], 400);
        }
    }
    public function delete_user(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->id);

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => trans('auth.user.deleted'),
            ], 200);
        
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete user: ' . $e->getMessage()], 400);
        }
    }

}
