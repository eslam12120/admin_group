<?php

namespace App\Http\Controllers\Api\Specialists;

use Exception;
use App\Models\City;
use App\Models\Rate;
use App\Models\Order;
use App\Models\Special;
use App\Models\Language;
use App\Models\Experience;
use App\Models\Government;
use App\Models\Specialist;
use App\Models\OrderNormal;
use App\Models\Certificates;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\SkillSpecialist;
use App\Models\SpecialistSpecial;
use App\Models\LanguageSpecialist;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderNormalSpecialist;
use App\Models\SpecialistVerification;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SpecialistController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:specialists,email',
            'languages' => 'nullable|array', // إذا كان عندك لغات متعددة
            'languages.*' => 'nullable|exists:languages,id', // التحقق من صحة اللغات
            'experiences' => 'nullable|array', // الخبرات المهنية
            'experiences.*.name' => 'nullable|string',
            'experiences.*.job' => 'nullable|string',
            'experiences.*.start_date' => 'nullable|date',
            'experiences.*.end_date' => 'nullable|date',
            'certificates' => 'nullable|array', // الشهادات
            'certificates.*.name' => 'nullable|string',
            'certificates.*.body' => 'nullable|string',
            'skills' => 'nullable|array', // المهارات
            'skills.*.name' => 'nullable|string',
            'skills.*.cyp' => 'nullable|string',
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
            if ($request->hasFile('image')) {
                $photo = $request->file('image');
                $ext = $photo->getClientOriginalName();
                $name = "spec-" . uniqid() . ".$ext";
                $photo->move(public_path('specialist_images/'), $name);
                // 1. إنشاء المختص في جدول specialists
                $specialist = Specialist::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'rate' => $request->rate ?? 0,
                    'yxp' => $request->exp_years ?? 0,
                    'price' => $request->price ?? 0,
                    'about_me' => $request->about_me,
                    'status' => $request->status,
                    'is_active' => $request->is_active ?? 0,
                    'city_id' => $request->city_id,
                    'gov_id' => $request->gov_id,
                    'image' => $name,
                ]);
                // 2. إضافة اللغات إلى جدول language_specialists
                if ($request->languages) {
                    foreach ($request->languages as $language_id) {
                        LanguageSpecialist::create([
                            'specialist_id' => $specialist->id,
                            'language_id' => $language_id,
                        ]);
                    }
                }

                // 3. إضافة الخبرات إلى جدول experiences
                if ($request->experiences) {
                    foreach ($request->experiences as $experience) {
                        Experience::create([
                            'specialist_id' => $specialist->id,
                            'name' => $experience['name'],
                            'job' => $experience['job'],
                            'start_date' => $experience['start_date'],
                            'end_date' => $experience['end_date'],
                            'still_now' => $experience['still_now'] ?? 0,
                        ]);
                    }
                }

                // 4. إضافة الشهادات إلى جدول certificates
                if ($request->certificates) {
                    foreach ($request->certificates as $certificate) {
                        Certificates::create([
                            'specialist_id' => $specialist->id,
                            'name' => $certificate['name'],
                            'body' => $certificate['body'],
                            'link' => $certificate['link'] ?? null,
                        ]);
                    }
                }

                // 5. إضافة المهارات إلى جدول skill_specialists
                if ($request->skills) {
                    foreach ($request->skills as $skill) {
                        SkillSpecialist::create([
                            'specialist_id' => $specialist->id,
                            'name' => $skill['name'],
                            'cyp' => $skill['cyp'],
                            'start_date' => $skill['start_date'],
                            'end_date' => $skill['end_date'],
                            'still_now' => $skill['still_now'] ?? 0,
                        ]);
                    }
                }
                // 6. إضافة التخصصات إلى جدول specialist_specials
                if ($request->specializations) {
                    foreach ($request->specializations as $specialization) {
                        SpecialistSpecial::create([
                            'specialist_id' => $specialist->id,
                            'special_id' => $specialization['special_id'],
                            'job_name_ar' => $specialization['job_name_ar'],
                            'job_name_en' => $specialization['job_name_ar'],
                        ]);
                    }
                }
                DB::commit();
            } else {
                // 1. إنشاء المختص في جدول specialists
                $specialist = Specialist::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'rate' => $request->rate ?? 0,
                    'yxp' => $request->exp_years ?? 0,
                    'price' => $request->price ?? 0,
                    'about_me' => $request->about_me,
                    'status' => $request->status,
                    'is_active' => $request->is_active ?? 0,
                    'city_id' => $request->city_id,
                    'gov_id' => $request->gov_id,
                ]);

                // 2. إضافة اللغات إلى جدول language_specialists
                if ($request->languages) {
                    foreach ($request->languages as $language_id) {
                        LanguageSpecialist::create([
                            'specialist_id' => $specialist->id,
                            'language_id' => $language_id,
                        ]);
                    }
                }

                // 3. إضافة الخبرات إلى جدول experiences
                if ($request->experiences) {
                    foreach ($request->experiences as $experience) {
                        Experience::create([
                            'specialist_id' => $specialist->id,
                            'name' => $experience['name'],
                            'job' => $experience['job'],
                            'start_date' => $experience['start_date'],
                            'end_date' => $experience['end_date'],
                            'still_now' => $experience['still_now'] ?? 0,
                        ]);
                    }
                }

                // 4. إضافة الشهادات إلى جدول certificates
                if ($request->certificates) {
                    foreach ($request->certificates as $certificate) {
                        Certificates::create([
                            'specialist_id' => $specialist->id,
                            'name' => $certificate['name'],
                            'body' => $certificate['body'],
                            'link' => $certificate['link'] ?? null,
                        ]);
                    }
                }

                // 5. إضافة المهارات إلى جدول skill_specialists
                if ($request->skills) {
                    foreach ($request->skills as $skill) {
                        SkillSpecialist::create([
                            'specialist_id' => $specialist->id,
                            'name' => $skill['name'],
                            'cyp' => $skill['cyp'],
                            'start_date' => $skill['start_date'],
                            'end_date' => $skill['end_date'],
                            'still_now' => $skill['still_now'] ?? 0,
                        ]);
                    }
                }

                // 6. إضافة التخصصات إلى جدول specialist_specials
                if ($request->specializations) {
                    foreach ($request->specializations as $specialization) {
                        SpecialistSpecial::create([
                            'specialist_id' => $specialist->id,
                            'special_id' => $specialization['special_id'],
                            'job_name_ar' => $specialization['job_name_ar'],
                            'job_name_en' => $specialization['job_name_ar'],
                        ]);
                    }
                }
                DB::commit();
            }
            return response()->json([
                'message' => 'Specialist created successfully',
                'data' => $specialist,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create specialist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:specialists,email',

        ], [
            'email.required' => trans('auth.email.register'),



        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        DB::beginTransaction();
        try {


            $user = specialist::where('email', $request->email)->first();
            $user->device_token = $request->device_token;
            $user->save();
            if ($user->is_verify == '0') {
                $user_otp = SpecialistVerification::where('specialist_id', $user->id)->latest()->first();
                if ($user_otp) {
                    $user_otp->delete();
                }

                $code = mt_rand(100000, 999999);
                $user_verification = SpecialistVerification::create([

                    'specialist_id' => $user->id,
                    'code' => $code,
                ]);
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

            return response()->json(['error' => 'Failed to login: ' . $e->getMessage()], 400);
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
                'phone.required' => trans('auth.phone.register'),
                'email.required' => trans('auth.code.required'),


            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }
        $user = Specialist::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => "هناك خطا في رقم الهاتف"], 422);
        }

        $user_otp = SpecialistVerification::where('specialist_id', $user->id)->latest()->first();
        if (!$user_otp) {
            return response()->json(['message' => "هناك خطا في الكود"], 422);
        }

        if ($user_otp->code == $request->code) {
            $token = auth()->guard('specialist-api')->login($user);
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
            'user' => Auth::guard('specialist-api')->user(),
            'is_verify' => 1,
        ]);
    }
    protected function respondWithToken_otp($token, $code, $user_id)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'status' => 200,
            'message' => trans('auth.login.success'),
            'user' => Specialist::where('id', $user_id)->first(),
            'code' => $code,
            'is_verify' => 0,
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
    public function get_all_data()
    {
        $city = City::select('id', 'name_' . app()->getLocale() . ' as name')->get();
        $government = Government::select('id', 'name_' . app()->getLocale() . ' as name')->get();
        $languages = Language::select('id', 'name_' . app()->getLocale() . ' as name')->get();
        $specials = Special::select('id', 'name_' . app()->getLocale() . ' as name', 'job_name_' . app()->getLocale() . ' as job_name')->get();
        return response()->json([
            'message' => 'success',
            'cities' =>  $city,
            'government' => $government,
            'languages' => $languages,
            'specials' => $specials
        ], 200);
    }
    public function get_all_orders_for_user(Request $request)
    {
        $specialistId = Auth::guard('specialist-api')->user()->id;

        // Get pending orders
        $orders = Order::with(['user', 'specialist.special_order'])
            ->where('specialist_id', $specialistId)
            ->where('status', 'pending')
            ->get();
        $orders->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        // Get pending normal orders
        $normalOrderData = OrderNormalSpecialist::where('specialist_id', $specialistId)->get();
        $normalOrders = OrderNormal::with(['user', 'specialist.special_order'])
            ->whereIn('id', $normalOrderData->pluck('order_id'))
            ->where('status', 'pending')
            ->get();
        $normalOrders->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            $order->audio_path_url = asset('uploads/files/' . $order->audio_path);
            $order->orderfiles->transform(function ($file) {
                if ($file->file_path) {
                    $file->file_url = asset('uploads/files/' . $file->file_path);
                }
                return $file;
            });
            return $order;
        });

        // Get active service orders
        $serviceOrders = OrderService::where('status', 'active')
            ->with([
                'user',
                'service_special' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name');
                },
            ])->get();
        $serviceOrders->transform(function ($order) {
            $order->audio_path_url = asset('uploads/files/' . $order->audio_path);
            if ($order->specialist) {
                $order->specialist->image_url = asset('specialist_images/' . $order->specialist->image);
            }
            $order->orderfiles->transform(function ($file) {
                if ($file->file_path) {
                    $file->file_url = asset('uploads/files/' . $file->file_path);
                }
                return $file;
            });
            return $order;
        });

        // Return the response
        return Response::json([
            'status' => 200,
            'message' => 'true',
            'orders' => $orders,
            'normal_orders' => $normalOrders,
            'service_orders' => $serviceOrders,
        ]);
    }

    public function getSpecialistData()
    {
        // Fetch specialist data based on ID, including city and government relations
        $specialist = Specialist::where('id', Auth::id())
            ->with([
                'city' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name');
                },
                'government' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name');
                },
            ])
            ->first();

        // Check if the specialist exists
        if (!$specialist) {
            return Response::json([
                'status' => 404,
                'message' => 'Specialist not found',
                'data' => null,
            ]);
        }

        // Add additional data to the specialist
        $specialist['specials'] = SpecialistSpecial::where('specialist_id', $specialist['id'])
            ->select('id', 'specialist_id', 'special_id')
            ->with(['specials' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name');
            }])
            ->get();

        $specialist['rates'] = Rate::with('user') // Load the related user for each rate
            ->where('specialist_id', $specialist['id'])
            ->select('id', 'specialist_id', 'rate', 'description', 'user_id')
            ->get()
            ->map(function ($rate) {
                // Add the user's image URL to the rate
                if ($rate->user) {
                    $rate->user['image_url'] = asset('images/users/' . $rate->user->image);
                }
                return $rate;
            });

        $specialist['languages'] = LanguageSpecialist::where('specialist_id', $specialist['id'])
            ->select('id', 'specialist_id', 'language_id')
            ->with(['languages' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name');
            }])
            ->get();

        $specialist['certificates'] = Certificates::where('specialist_id', $specialist['id'])->get();
        $specialist['skills'] = SkillSpecialist::where('specialist_id', $specialist['id'])->get();
        $specialist['experiences'] = Experience::where('specialist_id', $specialist['id'])->get();
        $specialist['image_url'] = asset('specialist_images/' . $specialist->image);

        //   $orders = Order::where('id', $id)->where('status', 'finished')->count();
        // Return the specialist data
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $specialist,
        ));
    }
    public function activate_account(Request $request)
    {

        Specialist::where('id', Auth::guard('specialist-api')->user()->id)->update([
            'is_active' => 1,
        ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',

        ));
    }
    public function unactivate_account(Request $request)
    {

        Specialist::where('id', Auth::guard('specialist-api')->user()->id)->update([
            'is_active' => 0,
        ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',

        ));
    }


    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => "required|email|unique:specialists,email,$id",
            'languages' => 'nullable|array',
            'languages.*' => 'nullable|exists:languages,id',
            'experiences' => 'nullable|array',
            'experiences.*.id' => 'nullable|exists:experiences,id',
            'experiences.*.name' => 'nullable|string',
            'experiences.*.job' => 'nullable|string',
            'experiences.*.start_date' => 'nullable|date',
            'experiences.*.end_date' => 'nullable|date',
            'certificates' => 'nullable|array',
            'certificates.*.id' => 'nullable|exists:certificates,id',
            'certificates.*.name' => 'nullable|string',
            'certificates.*.body' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*.id' => 'nullable|exists:skill_specialists,id',
            'skills.*.name' => 'nullable|string',
            'skills.*.cyp' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            $specialist = Specialist::findOrFail($id);

            if ($request->hasFile('image')) {
                $photo = $request->file('image');
                $ext = $photo->getClientOriginalName();
                $name = "spec-" . uniqid() . ".$ext";
                $photo->move(public_path('specialist_images/'), $name);
                $specialist->image = $name;
            }

            $specialist->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'rate' => $request->rate ?? $specialist->rate,
                'yxp' => $request->exp_years ?? $specialist->yxp,
                'price' => $request->price ?? $specialist->price,
                'about_me' => $request->about_me,
                'status' => $request->status,
                'is_active' => $request->is_active ?? $specialist->is_active,
                'city_id' => $request->city_id,
                'gov_id' => $request->gov_id,
            ]);

            // Update languages
            if ($request->languages) {
                LanguageSpecialist::where('specialist_id', $specialist->id)->delete();
                foreach ($request->languages as $language_id) {
                    LanguageSpecialist::create([
                        'specialist_id' => $specialist->id,
                        'language_id' => $language_id,
                    ]);
                }
            }

            // Update experiences
            if ($request->experiences) {
                foreach ($request->experiences as $experience) {
                    if (isset($experience['id'])) {
                        Experience::find($experience['id'])->update($experience);
                    } else {
                        Experience::create(array_merge($experience, ['specialist_id' => $specialist->id]));
                    }
                }
            }

            // Update certificates
            if ($request->certificates) {
                foreach ($request->certificates as $certificate) {
                    if (isset($certificate['id'])) {
                        Certificates::find($certificate['id'])->update($certificate);
                    } else {
                        Certificates::create(array_merge($certificate, ['specialist_id' => $specialist->id]));
                    }
                }
            }

            // Update skills
            if ($request->skills) {
                foreach ($request->skills as $skill) {
                    if (isset($skill['id'])) {
                        SkillSpecialist::find($skill['id'])->update($skill);
                    } else {
                        SkillSpecialist::create(array_merge($skill, ['specialist_id' => $specialist->id]));
                    }
                }
            }

            // Update specializations
            if ($request->specializations) {
                SpecialistSpecial::where('specialist_id', $specialist->id)->delete();
                foreach ($request->specializations as $specialization) {
                    SpecialistSpecial::create(array_merge($specialization, ['specialist_id' => $specialist->id]));
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Specialist updated successfully',
                'data' => $specialist,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update specialist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
