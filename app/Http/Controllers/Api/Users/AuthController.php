<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function register(Request $request)
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
            $code = mt_rand(100000, 999999);
            $user_verification = UserVerification::create([

                'user_id' => $user->id,
                'code' => $code,



            ]);


            // Send email to user
            //    Mail::to($request->email)->send(new OtpCode($code));

            // return Response::json(array(
            //     'status'=>200,
            //     'message'=>'true',
            //     'data'=>$allwishlist,
            // ));
            DB::commit();

            return response()->json([
                'code' => $code,
                'status' => '200',
                'message' => trans('auth.register.success'),
                'user' => $user,
            ]);
        } catch (Exception $e) {
            // Rollback all operations if an error occurs
            DB::rollBack();

            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 400);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
            'password' => 'required|string|max:50',
        ], [
            'phone.required' => trans('auth.phone.register'),

            'password.required' => trans('auth.password.register'),
            'password.min' => trans('auth.password.min.register'),
            'password.max' => trans('auth.password.max.register'),

        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        DB::beginTransaction();
        try {
            $credentials = request(['phone', 'password']);
            if (! $token = auth::guard('user-api')->attempt($credentials)) {

                return response()->json(['message' => trans('auth.login.failed')], 401);
            }

            $user = User::where('phone', $request->phone)->first();
            $user->device_token = $request->device_token;
            $user->save();
            $user_otp = UserVerification::where('user_id', $user->id)->latest()->first();
            if ($user_otp) {
                $user_otp->delete();
            }

            $code = mt_rand(100000, 999999);
            $user_verification = UserVerification::create([

                'user_id' => $user->id,
                'code' => $code,



            ]);



            // Send email to user
            //  Mail::to($request->email)->send(new OtpCode($code));

            // return Response::json(array(
            //     'status'=>200,
            //     'message'=>'true',
            //     'data'=>$allwishlist,
            // ));

            DB::commit();
            return response()->json([
                'code' => $code,
                'status' => '200',

                'phone' => $request->phone,
            ]);
        } catch (Exception $e) {
            // Rollback all operations if an error occurs
            DB::rollBack();

            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 400);
        }
    }
    public function check_otp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'code' => 'required',
                'phone' => 'required',

            ],
            [
                'phone.required' => trans('auth.phone.register'),
                'code.required' => trans('auth.code.required'),
                'code.exists' => trans('auth.code.exists'),

            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $user = User::where('phone', $request->phone)->first();
        if (!$user) {
            return response()->json(['message' => "هناك خطا في رقم الهاتف"], 422);
        }

        $user_otp = UserVerification::where('user_id', $user->id)->latest()->first();
        if (!$user_otp) {
            return response()->json(['message' => "هناك خطا في الكود"], 422);
        }

        if ($user_otp->code == $request->code) {
            $token = auth()->guard('user-api')->login($user);
            $user_otp->delete();
            $user->device_token = $request->device_token;
            $user->save();
            return $this->respondWithToken($token);
        } else {

            return response()->json(['message' => "هناك خطا في الكود"], 422);
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'status' => 200,
            'message' => trans('auth.login.success'),
            'user' => Auth::guard('user-api')->user(),
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
    public function getUserData()
    {
        $user = User::where('id', Auth::guard('user-api')->user()->id)->first();
        if ($user) {
            $user->image = asset('images/users/' . $user->image);
        }
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $user,
        ));
    }
}
