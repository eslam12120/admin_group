<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forget(Request $request)
    {


        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users',
            ],
            [
                'email.required' => trans('auth.email.register'),

                'email.exists' => trans('auth.login.exists')
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => 422], 422);
        }

        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $code = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create([
            'email' => $request->email,
            'code' => $code
        ]);

        //  $code=$codeData->code;
        // Send email to user
        // Mail::to($request->email)->send(new SendCodeResetPassword($code));


        return response(['message' => trans('passwords.sent'), 'code' => $code], 200);
    }
}
