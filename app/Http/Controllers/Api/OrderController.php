<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderNormal;
use App\Models\Specialist;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function orders(Request $request)
    {
        if ($request->status == 'pending') {
            $orders = Order::where('user_id', Auth::id())->where('status', 'pending')->with('specialist')->get();
        }
        if ($request->status == 'schedule') {
            $orders = Order::where('user_id', Auth::id())->where('status', 'schedule')->with('specialist')->get();
        }
        if ($request->status == 'finished') {
            $orders = Order::where('user_id', Auth::id())->where('status', 'finished')->with('specialist')->get();
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
  public function normal_orders(Request $request)
{
    // Retrieve orders based on the status
    $status = $request->status == 'pending' ? 'pending' : 'finished';
    $orders = OrderNormal::with(['ordernormal', 'orderfiles'])
        ->where('user_id', Auth::id())
        ->where('status', $status)
        ->get();

    // Transform the orders collection
    $orders->transform(function ($order) {
        $specialists = collect(); // Initialize an empty collection for specialists

        foreach ($order->ordernormal as $ordernormal) {
            if ($ordernormal->specialist_id) {
                // Fetch specialists for the given specialist_id
                $specialist = Specialist::where('id', $ordernormal->specialist_id)->get();

                foreach ($specialist as $spec) {
                    $spec->image_url = asset('specialist_images/' . $spec->image);
                }

                $specialists = $specialists->merge($specialist); // Merge specialists
            }
        }

        $order->specialists = $specialists; // Attach specialists to the order

        // Transform orderfiles to include file URLs
        $order->orderfiles->transform(function ($file) {
            if ($file->file_path) {
                $file->file_url = asset($file->file_path); // Add file URL
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

    public function  orderservices(Request $request)
    {
        if ($request->status == 'finished') {
            $orders = OrderService::where('user_id', Auth::id())->with(['specialist', 'orderfiles'])->where('status', 'finished')->get();
        }
        if ($request->status == 'pending') {
            $orders = OrderService::where('user_id', Auth::id())->with(['specialist', 'orderfiles'])->where('status', 'pending')->get();
        }
        if ($request->status == 'cancelled') {
            $orders = OrderService::where('user_id', Auth::id())->with(['specialist', 'orderfiles'])->where('status', 'cancelled')->get();
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
}
