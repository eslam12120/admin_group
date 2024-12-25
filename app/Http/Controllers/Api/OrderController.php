<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderNormal;
use App\Models\OrderService;

class OrderController extends Controller
{
    public function orders(Request $request)
    {
        if ($request->status == 'pending') {
            $orders = Order::where('status', 'pending')->with('specialist')->paginate(30);
        }
        if ($request->status == 'schedule') {
            $orders = Order::where('status', 'schedule')->with('specialist')->paginate(30);
        }
        if ($request->status == 'finished') {
            $orders = Order::where('status', 'finished')->with('specialist')->paginate(30);
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
    public function normal_orders(Request $request)
    {
        if ($request->status == 'pending') {
            $orders = OrderNormal::with(['ordernormal.specialist'])
                ->where('status', 'pending')
                ->paginate(30);
        } else {
            $orders = OrderNormal::with(['ordernormal.specialist'])
                ->where('status', 'finished')
                ->paginate(30);
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
    public function  orderservices(Request $request)
    {
        if ($request->status == 'finished') {
            $orders = OrderService::with('specialist')->where('status', 'finished')->paginate(30);
        }
        if ($request->status == 'pending') {
            $orders = OrderService::with('specialist')->where('status', 'pending')->paginate(30);
        }
        if ($request->status == 'cancelled') {
            $orders = OrderService::with('specialist')->where('status', 'cancelled')->paginate(30);
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
}
