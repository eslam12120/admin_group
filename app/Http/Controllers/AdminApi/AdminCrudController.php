<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminCrudController extends Controller
{
    //
    public function add_admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'name' => 'required',
           
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

            $admin = Admin::create([

                'email' => $request->email,
                'password' => Hash::make($request->password),
               
                'name' => $request->name,
             
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
    public function update_admin(Request $request)
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
            $user = Admin::findOrFail($request->id);

            // Update user attributes
            $user->update([
                'email' => $request->email ?? $user->email,
                'name' => $request->name ?? $user->name,
              
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => trans('auth.user.updated'),
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Failed to update admin: ' . $e->getMessage()], 400);
        }
    }
    public function delete_admin(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Admin::findOrFail($request->id);

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
