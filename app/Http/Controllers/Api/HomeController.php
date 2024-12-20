<?php

namespace App\Http\Controllers\Api;

use App\Models\Rate;
use App\Models\Order;
use App\Models\Coupoun;
use App\Models\Service;
use App\Models\Special;
use App\Models\Specialist;
use App\Models\UserCoupoun;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\SpecialistSpecial;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function Home(Request $request)
    {
        $lang = $request->lang;
        $specialists = Specialist::where('status', '1')->where('is_active', '1')->select('id', 'image')->get()->map(function ($specialist) {
            // Set only image_url
            $specialist->image_url = asset('specialist_images/' .  $specialist->image);
            return $specialist;
        });
        $nameColumn = $lang === 'ar' ? 'name_ar' : 'name_en';
        $services = Service::select('id', $nameColumn . ' as name', 'image')->where('active', '1')->get();
        $best_specialists = Specialist::with([
            'city' => function ($q) use ($lang) {
                $q->select('id', 'name_' . $lang . ' as name');
            }
        ])->where('status', '1')->orderBy('rate', 'desc')->take(5)->latest()->get()->map(function ($specialist) use ($lang) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
            $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
            return $specialist;
        });
        return response()->json([
            'message' => 'success',
            'services' =>  $services,
            'specialists' => $specialists,
            'best_specialists' => $best_specialists
        ], 200);
    }

    public function search_service_specialist(Request $request)
    {
        $lang = $request->lang;
        $nameColumn = $lang === 'ar' ? 'name_ar' : 'name_en';
        $specialists = Specialist::where('name', 'LIKE', '%' . $request->search . '%')->where('status', '1')->with([
            'city' => function ($q) use ($lang) {
                $q->select('id', 'name_' . $lang . ' as name');
            }
        ])->where('status', '1')->orderBy('rate', 'desc')->get()->map(function ($specialist) use ($lang) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
            $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
            return $specialist;
        });
        $services = Service::select('id', $nameColumn . ' as name', 'image')->where('name_ar', 'LIKE', '%' . $request->search . '%')->where('active', '1')->orWhere('active', '1')->where('name_en', 'LIKE', '%' . $request->search . '%')->get();
        return response()->json([
            'message' => 'success',
            'services' =>  $services,
            'specialists' => $specialists,
        ], 200);
    }

    public function get_specials(Request $request)
    {
        $lang = $request->lang;
        $nameColumn = $lang === 'ar' ? 'name_ar' : 'name_en';
        $specials = Special::select('id', $nameColumn . ' as name', 'image', 'job_name_' . $lang . ' as job_name')->where('active', '1')->get()->map(function ($special) use ($lang) {
            $special->image_url = asset('special_images/' . $special->image);
            return $special;
        });
        return response()->json([
            'message' => 'success',
            'data' =>  $specials,
        ], 200);
    }

    public function add_rate(Request $request)
    {
        DB::beginTransaction();
        Rate::create([
            'specialist_id' => $request->specialist_id,
            'user_id' => Auth::id(),
            'rate' => $request->rate,
            'description' => $request->description,
        ]);
        DB::commit();
        $rateData = DB::table('rates')
            ->selectRaw('SUM(rate) as total_rate, COUNT(*) as total_rows')
            ->where('specialist_id', $request->specialist_id)
            ->first();
        // Access the results
        $totalRate = $rateData->total_rate;
        $totalRows = $rateData->total_rows;
        $avg = $totalRate / $totalRows;
        Specialist::where('id', $request->specialist_id)->update([
            'rate' => floor($avg * 10) / 10
        ]);
        return response()->json([
            'message' => 'success',
        ], 200);
    }
    public function search_specialist(Request $request)
    {
        $lang = $request->lang;
        $nameColumn = $lang === 'ar' ? 'name_ar' : 'name_en';
        $specialists = Specialist::where('name', 'LIKE', '%' . $request->search . '%')->where('status', '1')->with([
            'city' => function ($q) use ($lang) {
                $q->select('id', 'name_' . $lang . ' as name');
            }
        ])->where('status', '1')->orderBy('rate', 'desc')->get()->map(function ($specialist) use ($lang) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
            $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
            return $specialist;
        });
        return response()->json([
            'message' => 'success',
            'data' => $specialists,
        ], 200);
    }
    public function sort_by(Request $request)
    {
        $lang = $request->lang;
        if ($request->sort == 'min') {
            $specialist = Specialist::where('status', '1')->with([
                'city' => function ($q) use ($lang) {
                    $q->select('id', 'name_' . $lang . ' as name');
                }
            ])->orderBy('price', 'asc')->get()->map(function ($specialist) use ($lang) {
                $specialist->image_url = asset('specialist_images/' . $specialist->image);
                $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
                $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
                return $specialist;
            });
        } elseif ($request->sort == 'max') {
            $specialist = Specialist::where('status', '1')->with([
                'city' => function ($q) use ($lang) {
                    $q->select('id', 'name_' . $lang . ' as name');
                }
            ])->orderBy('price', 'desc')->get()->map(function ($specialist) use ($lang) {
                $specialist->image_url = asset('specialist_images/' . $specialist->image);
                $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
                $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
                return $specialist;
            });
        } elseif ($request->sort == 'rating') {
            $specialist = Specialist::where('status', '1')->with([
                'city' => function ($q) use ($lang) {
                    $q->select('id', 'name_' . $lang . ' as name');
                }
            ])->orderBy('rate', 'desc')->get()->map(function ($specialist) use ($lang) {
                $specialist->image_url = asset('specialist_images/' . $specialist->image);
                $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
                $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
                return $specialist;
            });
        } else {
            $specialist = Specialist::where('status', '1')->with([
                'city' => function ($q) use ($lang) {
                    $q->select('id', 'name_' . $lang . ' as name');
                }
            ])->orderBy('price', 'asc')->get()->map(function ($specialist) use ($lang) {
                $specialist->image_url = asset('specialist_images/' . $specialist->image);
                $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
                $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
                return $specialist;
            });
        }
        return response()->json([
            'message' => 'success',
            'data' => $specialist,
        ], 200);
    }
    public function filter_by(Request $request)
    {
        $lang = $request->lang;
        $query = Specialist::query()->where('status', '1')->with([
            'city' => function ($q) use ($lang) {
                $q->select('id', 'name_' . $lang . ' as name');
            }
        ]);

        // Apply filters if provided
        if ($request->has('special_id')) {
            $special = SpecialistSpecial::where('special_id', $request->special_id)->pluck('specialist_id');
            $query->whereIn('id', $special);
        }

        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->has('gov_id')) {
            $query->where('gov_id', $request->gov_id);
        }

        if ($request->has('rate')) {
            $query->where('rate', $request->rate);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        // Sort specialists by the desired criteria (e.g., rating or price)
        $query->orderBy('rate', 'desc'); // Example: Sort by rating

        // Get paginated or filtered results
        $specialists = $query->get()->map(function ($specialist) use ($lang) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
            $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')->where('specialist_id', $specialist->id)->get();
            return $specialist;
        }); // or ->get();
        return response()->json([
            'message' => 'success',
            'data' => $specialists,
        ], 200);
    }

    public function add_order(Request $request)
    {
        $order = Order::create([
            'special_id' => $request->special_id,
            'specialist_id' => $request->specialist_id,
            'status' => 'pending',
            'type_payment' => $request->type_payment,
            'type_com' => $request->type_payment,
            'desc' => $request->desc,
            'address' => $request->address,
            'price' => $request->price,
            'paid_now' => $request->paid_now,
            'have_discount' => $request->have_discount,
            'code' => $request->code,
            'value_of_discount' => $request->value_of_discount,
            'user_id' => Auth::id(),
            'coupoun_id' => $request->coupoun_id,
        ]);
        if ($request->coupoun_id) {
            UserCoupoun::create([
                'user_id' => Auth::id(),
                'coupoun_id' => $request->coupoun_id,
            ]);
        }
        return response()->json([
            'message' => 'success',
            'data' => $order->id,
        ], 200);
    }
    public function add_coupoun(Request $request)
    {
        $co = Coupoun::where('is_active', '1')->where('code', $request->code)->whereDate('end_date', '>=', now())->whereDate('start_date', '<=', now())->first();
        if ($co) {
            if ($co->discount_type == 'percentage') {
                $price = $request->price - $co->discount_value * $request->price / 100;
            } else {
                $price = $request->price -  $co->discount_value;
            }
            $user = UserCoupoun::where('user_id', Auth::id())->where('coupoun_id', $co->id)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'success',
                    'data' => $co,
                    'price_after_discount' => $price,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'This Coupoun is Used Before',
                ], 422);
            }
        } else {
            return response()->json([
                'message' => '  Coupoun Wrong',
            ], 422);
        }
    }
    public function userNotifications()
    {
        $notifications = Notification::where('reciever_id', Auth::id())->orderBy('id', 'DESC')->simplePaginate(30);
        return response()->json([
            'data' => $notifications,
            'message' => 'success'
        ], 200);
    }

    public function read_notifications()
    {
        Notification::where('reciever_id', Auth::id())->orderBy('id', 'DESC')->update([
            'is_read' => '1'
        ]);
        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
