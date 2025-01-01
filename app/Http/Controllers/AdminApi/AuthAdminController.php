<?php

namespace App\Http\Controllers\AdminApi;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthAdminController extends Controller
{
    //
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:admins,email',
            'password' => 'required|string|max:50',
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
            $credentials = request(['email', 'password']);
            if (! $token = Auth::guard('admin-api')->attempt($credentials)) {

                return response()->json(['message' => trans('هناك خطا في كلمه السر')], 401);
            }
            return $this->respondWithToken($token);
            
        } catch (Exception $e) {
            // Rollback all operations if an error occurs
            DB::rollBack();

            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 400);
        }
    }
   
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'status' => 200,
            'message' => trans('auth.login.success'),
            'user' => Auth::guard('admin-api')->user(),
        ]);
    }
   
  
    public function logout(Request $request)
    {
        $token = $request->header('auth-token');
        if ($token) {
            try {

                JWTAuth::setToken($token)->invalidate(); //logout
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['message' => "something went wrong"], 422);
            }
            return response()->json(['message' => trans('auth.logout.success')]);
        } else {
            return response()->json(['message' => "something went wrong"], 422);
        }
    }
}
