<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Specialist;
use App\Models\OrderNormal;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\RejectionReason;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public function orders(Request $request)
    {
        $status = $request->status;
        $validStatuses = ['pending', 'schedule', 'finished'];

        if (in_array($status, $validStatuses)) {
            $orders = Order::where('user_id', Auth::id())
                ->where('status', $status)
                ->with('specialist.special_order') // Include specialist and special
                ->get();

            $orders->transform(function ($order) {
                if ($order->specialist) {
                    $order->specialist->image_url = asset('specialist_images/' .  $order->specialist->image);
                }
                return $order;
            });

            return response()->json([
                'message' => 'success',
                'data' => $orders,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid status',
            'data' => [],
        ], 400);
    }


    public function normal_orders(Request $request)
    {
        $status = $request->status == 'pending' ? 'pending' : 'finished';
        $orders = OrderNormal::with(['ordernormal', 'orderfiles'])
            ->where('user_id', Auth::id())
            ->where('status', $status)
            ->get();

        $orders->transform(function ($order) {
            $specialists = collect();
            $order->audio_path_url = asset('uploads/files/' . $order->audio_path);
            foreach ($order->ordernormal as $ordernormal) {
                if ($ordernormal->specialist_id) {
                    $specialist = Specialist::where('id', $ordernormal->specialist_id)
                        ->with('special_order') // Include the 'special' relation
                        ->get();

                    foreach ($specialist as $spec) {
                        $spec->image_url = asset('specialist_images/' . $spec->image);
                    }

                    $specialists = $specialists->merge($specialist);
                }
            }

            $order->specialists = $specialists;

            $order->orderfiles->transform(function ($file) {
                if ($file->file_path) {
                    $file->file_url = asset('uploads/files/' . $file->file_path);
                }
                return $file;
            });

            return $order;
        });

        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }

    public function orderservices(Request $request)
    {
        $status = $request->status;
        $orders = OrderService::where('user_id', Auth::id())
            ->with(['specialist.special_order', 'orderfiles']) // Include 'special' relation
            ->where('status', $status)
            ->get();
        $orders->transform(function ($order) {
            $order->audio_path_url = asset('uploads/files/' . $order->audio_path);
            if ($order->specialist) {
                $order->specialist->image_url = asset('specialist_images/' .  $order->specialist->image);
            }
            $order->orderfiles->transform(function ($file) {
                if ($file->file_path) {
                    $file->file_url = asset('uploads/files/' . $file->file_path);
                }
                return $file;
            });
            return $order;
        });

        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
    public function service_order_finished(Request $request)
    {

        $orders = OrderService::where('id', $request->order_id)->Update([
            'specialist_id' => Auth::guard('user-api')->user()->id,
            'status' => 'finished ',

        ]);

        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function service_order_cancelled(Request $request)
    {

        $orders = OrderService::where('id', $request->order_id)->Update([
            'specialist_id' => Auth::guard('user-api')->user()->id,
            'status' => 'cancelled',

        ]);
        RejectionReason::create([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'specialist_id' => Auth::guard('user-api')->user()->id,
            'type' => 'service_order',

        ]);

        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
}
