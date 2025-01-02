<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\Experience;
use App\Models\Specialist;
use App\Models\Certificates;
use Illuminate\Http\Request;
use App\Models\SkillSpecialist;
use App\Models\SpecialistSpecial;
use App\Models\LanguageSpecialist;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SpecialistController extends Controller
{
    public function index()
    {
        $specialists = Specialist::whereIn('active', ['1', '0'])->with(['languages', 'experiences', 'certificates', 'skills', 'specializations'])->paginate(30);
        $specialists->getCollection()->transform(function ($specialist) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            return $specialist;
        });
        return response()->json([
            'message' => 'Specialists retrieved successfully',
            'data' => $specialists,
        ], 200);
    }
    public function show($id)
    {
        $specialist = Specialist::with(['languages', 'experiences', 'certificates', 'skills', 'specializations'])->find($id);

        if (!$specialist) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }
        if ($specialist) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
        } else {
            $specialist->image_url = null;
        }
        return response()->json([
            'message' => 'Specialist retrieved successfully',
            'data' => $specialist,
        ], 200);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:specialists,email',
            'languages' => 'nullable|array',
            'languages.*' => 'nullable|exists:languages,id',
            'experiences' => 'nullable|array',
            'experiences.*.name' => 'nullable|string',
            'experiences.*.job' => 'nullable|string',
            'experiences.*.start_date' => 'nullable|date',
            'experiences.*.end_date' => 'nullable|date',
            'certificates' => 'nullable|array',
            'certificates.*.name' => 'nullable|string',
            'certificates.*.body' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*.name' => 'nullable|string',
            'skills.*.cyp' => 'nullable|string',
        ], [
            'email.required' => trans('auth.email.register'),
            'password.required' => trans('auth.password.register'),
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Create the Specialist
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

            // 2. Add languages to the 'language_specialists' table
            if ($request->languages) {
                foreach ($request->languages as $language_id) {
                    LanguageSpecialist::create([
                        'specialist_id' => $specialist->id,
                        'language_id' => $language_id,
                    ]);
                }
            }

            // 3. Add experiences to the 'experiences' table
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

            // 4. Add certificates to the 'certificates' table
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

            // 5. Add skills to the 'skills_specialists' table
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

            // 6. Add specializations to the 'specialist_specials' table
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

    public function update(Request $request, $id)
    {
        $specialist = Specialist::find($id);

        if (!$specialist) {
            return response()->json(['message' => 'Specialist not found'], 404);
        }

        DB::beginTransaction();

        try {
            // 1. Update the Specialist data
            $specialist->update([
                'name' => $request->name ?? $specialist->name,
                'phone' => $request->phone ?? $specialist->phone,
                'email' => $request->email ?? $specialist->email,
                'rate' => $request->rate ?? $specialist->rate,
                'yxp' => $request->exp_years ?? $specialist->yxp,
                'price' => $request->price ?? $specialist->price,
                'about_me' => $request->about_me ?? $specialist->about_me,
                // 'image' => $request->image ?? $specialist->image,
                'status' => $request->status ?? $specialist->status,
                'is_active' => $request->is_active ?? $specialist->is_active,
                'city_id' => $request->city_id ?? $specialist->city_id,
                'gov_id' => $request->gov_id ?? $specialist->gov_id,
            ]);

            // 2. Update languages
            if ($request->languages) {
                $specialist->languages()->sync($request->languages); // Sync ensures we replace existing with new
            }

            // 3. Update experiences
            if ($request->experiences) {
                $specialist->experiences()->delete(); // Delete old experiences
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

            // 4. Update certificates
            if ($request->certificates) {
                $specialist->certificates()->delete(); // Delete old certificates
                foreach ($request->certificates as $certificate) {
                    Certificates::create([
                        'specialist_id' => $specialist->id,
                        'name' => $certificate['name'],
                        'body' => $certificate['body'],
                        'link' => $certificate['link'] ?? null,
                    ]);
                }
            }

            // 5. Update skills
            if ($request->skills) {
                $specialist->skills()->delete(); // Delete old skills
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

            // 6. Update specializations
            if ($request->specializations) {
                $specialist->specializations()->delete(); // Delete old specializations
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
    public function destroy($id)
    {
        $specialist = Specialist::find($id);
        $specialist->update([
            'status' => '2'
        ]);
        return response()->json([
            'message' => 'Specialist updated successfully',
            'data' => $specialist,
        ], 200);
    }
}
