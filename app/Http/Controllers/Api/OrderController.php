<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderNormal;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function orders(Request $request)
    {
        if ($request->status == 'pending') {
            $orders = Order::where('user_id',Auth::id())->where('status', 'pending')->with('specialist')->paginate(30);
        }
        if ($request->status == 'schedule') {
            $orders = Order::where('user_id',Auth::id())->where('status', 'schedule')->with('specialist')->paginate(30);
        }
        if ($request->status == 'finished') {
            $orders = Order::where('user_id',Auth::id())->where('status', 'finished')->with('specialist')->paginate(30);
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
    public function normal_orders(Request $request)
    {
        if ($request->status == 'pending') {
            $orders = OrderNormal::with(['ordernormal.specialist'])->where('user_id',Auth::id())->
                ->where('status', 'pending')
                ->paginate(30);
        } else {
            $orders = OrderNormal::where('user_id',Auth::id())->with(['ordernormal.specialist'])
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
            $orders = OrderService::where('user_id',Auth::id())->with('specialist')->where('status', 'finished')->paginate(30);
        }
        if ($request->status == 'pending') {
            $orders = OrderService::where('user_id',Auth::id())->with('specialist')->where('status', 'pending')->paginate(30);
        }
        if ($request->status == 'cancelled') {
            $orders = OrderService::where('user_id',Auth::id())->with('specialist')->where('status', 'cancelled')->paginate(30);
        }
        return response()->json([
            'message' => 'success',
            'data' => $orders,
        ], 200);
    }
}
