<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Http\Controllers\Controller;
use App\Models\Certificates;
use App\Models\City;
use App\Models\Experience;
use App\Models\Government;
use App\Models\Language;
use App\Models\LanguageSpecialist;
use App\Models\Order;
use App\Models\SkillSpecialist;
use App\Models\Special;
use App\Models\Specialist;
use App\Models\SpecialistSpecial;
use App\Models\SpecialistVerification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            // 1. إنشاء المختص في جدول specialists
            $specialist = Specialist::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'rate' => $request->rate ?? 0,
                'yxp' => $request->exp_years ?? 0,
                'price' => $request->price ?? 0,
                'about_me' => $request->about_me,
                'image' => $request->image,
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
                        'job_name_en' => $specialization['job_name_en'],
                    ]);
                }
            }

            DB::commit();

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
                return $this->respondWithToken_otp($token, $code);
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
        ]);
    }
    protected function respondWithToken_otp($token, $code)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'status' => 200,
            'message' => trans('auth.login.success'),
            'user' => Auth::guard('specialist-api')->user(),
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
    public function get_all_data()
    {
        $city = City::select('id', 'name_' . app()->getLocale() . ' as name')->get();
        $government = Government::select('id', 'name_' . app()->getLocale() . ' as name')->get();
        $languages = Language::select('id', 'name_' . app()->getLocale() . ' as name')->get();
        $specials = Special::select('id', 'name_' . app()->getLocale() . ' as name', 'job_name_ar', 'job_name_en')->get();
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
        $orders = Order::with(['user', 'coupon'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->get();
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $orders,
        ));
    }
    public function getSpecialistData()
    {
        // جلب بيانات المختص بناءً على الـ ID الموجود في التوكن (المستخدم عبر الـ API)
        $specialist = Specialist::with(['city' => function ($q) {
            $q->select('id', 'name_' . app()->getLocale() . ' as name');
        }, 'government' => function ($q) {
            $q->select('id', 'name_' . app()->getLocale() . ' as name');
        },])->where('id', Auth::guard('specialist-api')->user()->id)->get();
        $specialist->map(function ($specialist) {

            $specialist['specials'] = SpecialistSpecial::where('specialist_id', $specialist['id'])->select('id', 'specialist_id', 'special_id')->with(['specials' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name');
            }])->get();
        });
        $specialist->map(function ($specialist) {

            $specialist['languages'] = LanguageSpecialist::where('specialist_id', $specialist['id'])->select('id', 'specialist_id', 'language_id')->with(['languages' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name');
            }])->get();
        });
        $specialist->map(function ($specialist) {

            $specialist['certificates'] = Certificates::where('specialist_id', $specialist['id'])->get();
        });
        $specialist->map(function ($specialist) {

            $specialist['skills'] = SkillSpecialist::where('specialist_id', $specialist['id'])->get();
        });
        $specialist->map(function ($specialist) {

            $specialist['experiences'] = Experience::where('specialist_id', $specialist['id'])->get();
        });

        // التحقق من وجود المختص
        // if ($specialist) {
        //     // إضافة الرابط الكامل للصورة
        //     $specialist->image = asset('images/specialists/' . $specialist->image);
        // }

        // إرجاع البيانات في الاستجابة
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $specialist,
        ));
    }
}
