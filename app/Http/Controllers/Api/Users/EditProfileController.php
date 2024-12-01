<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class EditProfileController extends Controller
{
    public function Editprofile(Request $request)
    {
        $userId = Auth::guard('user-api')->user()->id;

        $validator = Validator::make(
            $request->all(),
            [

                'name' => 'required|string',
                'email'=> 'required|unique:users,email,' . $userId
              
                //   'image' => 'required',
            ],
            [
                'name.required' => trans('editProfile.nameRequired'),
                'name.string' => trans('editProfile.nameString'),
                //       'image.required'=>trans('editProfile.photoRequired'),

            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 422
            ]);
        }

        try {
            $user = User::where('id', Auth::guard('user-api')->user()->id)->first();
            DB::beginTransaction();
            $name = $user->image;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $ext = $photo->getClientOriginalName();
                $name = "user-" . uniqid() . ".$ext";
                $photo->move(public_path('images/users'), $name);
            }

            $users = User::where('id', auth('user-api')->user()->id)->update([
                
                'name' => $request->name,
                'email' => $request->email,
                'is_completed' => '1',
                'image' => $name,
            ]);
            DB::commit();
            return Response::json(array(
                'status' => 200,
                'message' => trans('msg.updateSuccess'),
            ));
        } catch (Exception $e) {
            // Rollback all operations if an error occurs
            DB::rollBack();

            return response()->json(['error' => 'Failed to create user: ' . $e->getMessage()], 500);
        }
    }
    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|max:100',
            'confirm_password' => 'required|same:password'
        ], [

            'password.required' => trans('editProfile.passwordRequired'),
            'confirm_password.required' => trans('editProfile.confirm_passwordRequired'),
            'confirm_password.same' => trans('editProfile.confirm_passwordSame'),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 422
            ]);
        }
        $user = Auth::guard('user-api')->user();

        if (Hash::check($request->old_password, $user->password)) {
            User::findOrfail(Auth::guard('user-api')->user()->id)->update([
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'message' => trans('msg.pwSuccess'),
            ], 200);
        } else {
            return response()->json([
                'message' => trans('msg.pwError'),
            ], 400);
        }
    }
}
