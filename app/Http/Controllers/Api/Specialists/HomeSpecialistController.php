<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Http\Controllers\Controller;
use App\Models\Negotation;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\OrderNormal;
use App\Models\OrderNormalSpecialist;
use App\Models\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class HomeSpecialistController extends Controller
{
    public function getdata()
    {
        $data = OrderService::where('status', 'active')->with([
            'service_special' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name');
            }
        ])->get();

        $data->map(function ($data) {
            $data['files'] = OrderFile::where('order_id', $data['id'])->get();
        });

        return Response::json([
            'status' => 200,
            'message' => 'true',
            'data' => $data,
        ]);
    }

    public function get_data_by_id(Request $request)
    {
        $data = OrderService::where('id', $request->id)->with([
            'service_special' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name');
            }
        ])->get();

        $data->map(function ($data) {
            $data['files'] = OrderFile::where('order_id', $data['id'])->get();
        });

        return Response::json([
            'status' => 200,
            'message' => 'true',
            'data' => $data,
        ]);
    }

    public function add_negotation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'specialist_id' => 'nullable|integer',
            'time' => 'nullable',
            'price' => 'nullable',
            'status' => 'nullable|string|max:255',
        ], [
            'order_id.required' => trans('auth.email.register'),
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        Negotation::create([
            'order_id' => $request->order_id,
            'user_id' => $request->user_id,
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'time' => $request->time,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return Response::json([
            'status' => 200,
            'message' => 'created successfully',
        ]);
    }

    public function get_all_schadule_orders()
    {
        $data = Order::with(['user'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('status', 'schedule')->get();

        $data->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        return Response::json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function get_all_finished_orders()
    {
        $data = Order::with(['user'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('status', 'finished')->get();

        $data->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        return Response::json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function get_all_cancelled_orders()
    {
        $data = Order::with(['user'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('status', 'cancelled')->get();

        $data->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        return Response::json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function get_all_pending_service_orders()
    {
        $data = OrderService::with(['user', 'orderfiles'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('status', 'pending')->get();

        $data->transform(function ($order) {
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
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        return Response::json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function get_all_finished_service_orders()
    {
        $data = OrderService::with(['user'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('status', 'finished')->get();

        $data->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        return Response::json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function get_all_cancelled_service_orders()
    {
        $data = OrderService::with(['user'])->where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('status', 'cancelled')->get();

        $data->transform(function ($order) {
            if ($order->user) {
                $order->user->image_url = asset('images/users/' . $order->user->image);
            }
            return $order;
        });

        return Response::json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function get_all_finished_normal_orders()
    {
        $data = OrderNormalSpecialist::where('specialist_id', Auth::guard('specialist-api')->user()->id)->get();
        $normal_order = OrderNormal::with(['user'])->whereIn('id', $data->pluck('order_id'))->where('status', 'finished')->get();

        $normal_order->transform(function ($order) {
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

        return Response::json([
            'status' => 200,
            'data' => $normal_order,
        ]);
    }

    public function get_all_cancelled_normal_orders()
    {
        $data = OrderNormalSpecialist::where('specialist_id', Auth::guard('specialist-api')->user()->id)->get();
        $normal_order = OrderNormal::with(['user'])->whereIn('id', $data->pluck('order_id'))->where('status', 'cancelled')->get();

        $normal_order->transform(function ($order) {
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

        return Response::json([
            'status' => 200,
            'data' => $normal_order,
        ]);
    }
}
