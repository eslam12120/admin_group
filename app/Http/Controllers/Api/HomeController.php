<?php

namespace App\Http\Controllers\Api;

use App\Models\Rate;
use App\Models\Order;
use App\Models\Coupoun;
use App\Models\Service;
use App\Models\Special;
use App\Models\OrderFile;
use App\Models\Experience;
use App\Models\Specialist;
use App\Models\OrderNormal;
use App\Models\UserCoupoun;
use App\Models\Certificates;
use App\Models\Notification;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\ServiceSpecial;
use App\Models\SkillSpecialist;
use App\Models\SpecialistSpecial;
use App\Models\LanguageSpecialist;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Negotation;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderNormalSpecialist;
use Illuminate\Support\Facades\Response;

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
        $services = Service::select('id', $nameColumn . ' as name', 'image', 'description_' . $lang . ' as description')->where('active', '1')->get();
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
        $services = Service::select('id', $nameColumn . ' as name', 'image', 'description_' . $lang . ' as description')->where('name_ar', 'LIKE', '%' . $request->search . '%')->where('active', '1')->orWhere('active', '1')->where('name_en', 'LIKE', '%' . $request->search . '%')->get();
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
        $specials = Special::select('id', $nameColumn . ' as name', 'image', 'job_name_' . $lang . ' as job_name', 'description_' . $lang . ' as description')->where('active', '1')->get()->map(function ($special) use ($lang) {
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
        $sortColumn = 'price'; // Default sorting column
        $sortOrder = 'asc';    // Default sorting order

        // Determine sorting criteria
        if ($request->sort == 'min') {
            $sortColumn = 'price';
            $sortOrder = 'asc';
        } elseif ($request->sort == 'max') {
            $sortColumn = 'price';
            $sortOrder = 'desc';
        } elseif ($request->sort == 'rating') {
            $sortColumn = 'rate';
            $sortOrder = 'desc';
        }

        // Fetch and paginate specialists
        $specialists = Specialist::where('status', '1')
            ->with([
                'city' => function ($q) use ($lang) {
                    $q->select('id', 'name_' . $lang . ' as name');
                }
            ])
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(30)
            ->through(function ($specialist) use ($lang) {
                $specialist->image_url = asset('specialist_images/' . $specialist->image);
                $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
                $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')
                    ->where('specialist_id', $specialist->id)
                    ->get();
                return $specialist;
            });

        return response()->json([
            'message' => 'success',
            'data' => $specialists,
        ], 200);
    }
    public function filter_by(Request $request)
    {
        $lang = $request->lang;
        $query = Specialist::query()
            ->where('status', '1')
            ->with([
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

        // Sort specialists by the desired criteria (default: rating)
        $query->orderBy('rate', 'desc');

        // Fetch and paginate specialists
        $specialists = $query->paginate(30)->through(function ($specialist) use ($lang) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            $specialist->rate_count = Rate::where('specialist_id', $specialist->id)->count();
            $specialist->job = SpecialistSpecial::select('id', 'job_name_' . $lang . ' as name')
                ->where('specialist_id', $specialist->id)
                ->get();
            return $specialist;
        });

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
            'type_com' => $request->type_com,
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


    public function getSpecialistData($id)
    {
        // Fetch specialist data based on ID, including city and government relations
        $specialist = Specialist::where('id', $id)
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

        $orders_1 = Order::where('specialist_id', $specialist->id)->where('status', 'finished')->count();
        $orders_2 = OrderNormalSpecialist::where('specialist_id', $specialist->id)->count();
        $orders_3 = OrderService::where('specialist_id', $specialist->id)->where('status', 'finished')->count();
        $orders =  $orders_1 +  $orders_2 +  $orders_3;
        // Return the specialist data
        return Response::json([
            'status' => 200,
            'message' => 'true',
            'data' => $specialist,
            'orders_count' => $orders,
        ]);
    }


    public function add_order_normal(Request $request)
    {
        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('uploads/audio', 'public');
        }
        // Handle general file upload
        $filePaths = [];
        if ($request->hasFile('file') && is_array($request->file)) {
            foreach ($request->file('file') as $file) {
                // Store each file and save its path
                $filePaths[] = $file->store('uploads/files', 'public');
            }
        }
        $order = OrderNormal::create([
            'special_id' => $request->special_id,
            'status' => 'pending',
            'type_payment' => $request->type_payment,
            'desc' => $request->desc,
            'address' => $request->address,
            'price' => $request->price,
            'paid_now' => $request->paid_now,
            'have_discount' => $request->have_discount,
            'code' => $request->code,
            'value_of_discount' => $request->value_of_discount,
            'user_id' => Auth::id(),
            'coupoun_id' => $request->coupoun_id,
            'audio_path' => $audioPath, // Save audio path
            // 'file_path' => $filePath, // Save file path
        ]);
        if ($request->coupoun_id) {
            UserCoupoun::create([
                'user_id' => Auth::id(),
                'coupoun_id' => $request->coupoun_id,
            ]);
        }
        if ($request->specialist_id && is_array($request->specialist_id)) {
            foreach ($request->specialist_id as $specialistId) {
                OrderNormalSpecialist::create([
                    'specialist_id' => $specialistId,
                    'order_id' => $order->id, // Ensure $order->id is valid
                ]);
            }
        }
        if ($filePaths) {
            foreach ($filePaths as $filePath) {
                OrderFile::create([
                    'order_id' => $order->id,  // The order the file is related to
                    'file_path' => $filePath,   // The file path
                    'type' => 'normal'
                ]);
            }
        }
        return response()->json([
            'message' => 'success',
            'data' => $order->id,
        ], 200);
    }



    public function specialistNotifications()
    {
        $notifications = Notification::where('specialist_id', Auth::id())->orderBy('id', 'DESC')->simplePaginate(30);
        return response()->json([
            'data' => $notifications,
            'message' => 'success'
        ], 200);
    }

    public function specialistread_notifications()
    {
        Notification::where('specialist_id', Auth::id())->orderBy('id', 'DESC')->update([
            'is_read' => '1'
        ]);
        return response()->json([
            'message' => 'success'
        ], 200);
    }

    public function services_specials()
    {

        $services = ServiceSpecial::where('active', '1')->select('id', 'name_' . app()->getLocale() . ' as name', 'description_' . app()->getLocale() . ' as description', 'price', 'image')->orderBy('id', 'DESC')->simplePaginate(30);
        return response()->json([
            'data' => $services,
            'message' => 'success'
        ], 200);
    }

    public function add_order_service(Request $request)
    {
        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('uploads/audio', 'public');
        }
        // Handle general file upload
        $filePaths = [];
        if ($request->hasFile('file') && is_array($request->file)) {
            foreach ($request->file('file') as $file) {
                // Store each file and save its path
                $filePaths[] = $file->store('uploads/files', 'public');
            }
        }
        $order = OrderService::create([
            'status' => 'active',
            'type_payment' => $request->type_payment,
            'desc' => $request->desc,
            'address' => $request->address,
            'price' => $request->price,
            'paid_now' => $request->paid_now,
            'have_discount' => $request->have_discount,
            'code' => $request->code,
            'value_of_discount' => $request->value_of_discount,
            'user_id' => Auth::id(),
            'coupoun_id' => $request->coupoun_id,
            'service_special_id' => $request->service_special_id,
            'audio_path' => $audioPath, // Save audio path
            //   'file_path' => $filePath, // Save file path
        ]);
        if ($request->coupoun_id) {
            UserCoupoun::create([
                'user_id' => Auth::id(),
                'coupoun_id' => $request->coupoun_id,
            ]);
        }
        if ($filePaths) {
            foreach ($filePaths as $filePath) {
                OrderFile::create([
                    'order_id' => $order->id,  // The order the file is related to
                    'file_path' => $filePath,   // The file path
                    'type' => 'services'
                ]);
            }
        }
        return response()->json([
            'message' => 'success',
            'data' => $order->id,
        ], 200);
    }
    public function specialist_offers()
    {
        $services = Negotation::with('specialist')->where('user_id', Auth::id())->where('status', 'pending')->orderBy('id', 'DESC')->simplePaginate(30);
        $services->getCollection()->transform(function ($item) {
            if ($item->specialist) {
                $item->specialist->image_url = asset('images/specialists/' . $item->specialist->image);
            }
            return $item;
        });
        return response()->json([
            'data' => $services,
            'message' => 'success'
        ], 200);
    }
    public function approve_offers(Request $request)
    {
        Negotation::where('id', $request->id)->update([
            'status' => 'approved',
        ]);
        $specialist = Negotation::where('id', $request->id)->first();
        OrderService::where('id', $request->order_id)->update([
            'status' => 'pending',
            'specialist_id' => $specialist->specialist_id ?? null
        ]);
        return response()->json([
            'message' => 'success'
        ], 200);
    }
    public function reject_offers(Request $request)
    {
        Negotation::where('id', $request->id)->update([
            'status' => 'rejected',
        ]);
        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
