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
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpCode;

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


             //Send email to user
                Mail::to($request->email)->send(new OtpCode($code));

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
            'email' => 'required|exists:users,email',
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
            $credentials = request(['email', 'password']);
            if (! $token = auth::guard('user-api')->attempt($credentials)) {

                return response()->json(['message' => trans('هناك خطا في كلمه السر')], 401);
            }

            $user = User::where('email', $request->email)->first();
            $user->device_token = $request->device_token;
            $user->save();
            if ($user->is_verify == '0') {
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
                  Mail::to($request->email)->send(new OtpCode($code));

                // return Response::json(array(
                //     'status'=>200,
                //     'message'=>'true',
                //     'data'=>$allwishlist,
                // ));

                DB::commit();
                $token = null;
                return $this->respondWithToken_otp($token, $code, $user->id);
            } else {
                $token = auth()->guard('user-api')->login($user);
                return $this->respondWithToken($token);
            }
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
                'email' => 'required',

            ],
            [
                'email.required' => trans('auth.phone.register'),
                'code.required' => trans('auth.code.required'),
                'code.exists' => trans('auth.code.exists'),

            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => "هناك خطا في الايميل"], 422);
        }

        $user_otp = UserVerification::where('user_id', $user->id)->latest()->first();
        if (!$user_otp) {
            return response()->json(['message' => "هناك خطا في الكود"], 422);
        }

        if ($user_otp->code == $request->code) {
            $token = auth()->guard('user-api')->login($user);
            $user_otp->delete();
            $user->device_token = $request->device_token;
            $user->is_verify = '1';
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
    protected function respondWithToken_otp($token, $code, $user_id)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'status' => 200,
            'message' => trans('auth.login.success'),
            'user' => User::where('id', $user_id)->first(),
            'code' => $code
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
