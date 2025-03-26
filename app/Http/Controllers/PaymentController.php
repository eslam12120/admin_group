<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderNormal;
use App\Models\OrderService;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function payment_url(Request $request)
    {
        if($request->type == 'order'){
            $order = Order::where('id', $request->order_id)->first();
            $currentTime = Carbon::now();
            if (!$order) {
                return response()->json([
                    'message' => 'Order Not Found',
                ], 400);
            }

            if ($order->status_payment == 'paid') {
                $url = url('successpayment');
                return response()->json([
                    'message' => 'success',
                    'data' => $url,
                ], 200);
            }
            $orderTime = Carbon::parse($order->created_at);
            if ($currentTime->diffInMinutes($orderTime) > 15) {
                $payment = Payment::where('kind','order')->where('order_id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
                if ($payment) {
                    $payment->delete();
                }
                $order->delete();
                return response()->json([
                    'message' => 'تم تجاوز الحد الاقصي للوقت المسموح للدفع برجاء الطلب  مرة اخري ',
                ], 400);
            }
            $payment = Payment::where('kind','order')->where('order_id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
            if ($payment) {
                $url = url('payment/' . $request->order_id . '/' . $payment->url . '/' . $order->user_id . '/' . $payment->id . '/' . $request->device_type);
                return response()->json([
                    'message' => 'Success',
                    'data' => $url,
                ], 200);
            }
            $code = Str::uuid();
            $payment = Payment::create([
                'user_id' => $order->user_id,
              //  'specialist_id' => $order->specialist_id,
                'order_id' => $order->id,
                'url' => $code,
                'status' => 'opened_link',
                'kind'=>'order'
            ]);
            $url = url('payment/' . $request->order_id . '/' . $code . '/' . $order->user_id . '/' . $payment->id . '/' . $request->device_type);
            return response()->json([
                'message' => 'Success',
                'data' => $url,
            ], 200);
        }
        elseif($request->type == 'normal'){
            $order = OrderNormal::where('id', $request->order_id)->first();
            $currentTime = Carbon::now();
            if (!$order) {
                return response()->json([
                    'message' => 'Order Not Found',
                ], 400);
            }

            if ($order->status_payment == 'paid') {
                $url = url('successpayment');
                return response()->json([
                    'message' => 'success',
                    'data' => $url,
                ], 200);
            }
            $orderTime = Carbon::parse($order->created_at);
            if ($currentTime->diffInMinutes($orderTime) > 15) {
                $payment = Payment::where('kind','normal_order')->where('order_id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
                if ($payment) {
                    $payment->delete();
                }
                $order->delete();
                return response()->json([
                    'message' => 'تم تجاوز الحد الاقصي للوقت المسموح للدفع برجاء الطلب  مرة اخري ',
                ], 400);
            }
            $payment = Payment::where('kind','normal_order')->where('order_id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
            if ($payment) {
                $url = url('payment/' . $request->order_id . '/' . $payment->url . '/' . $order->user_id . '/' . $payment->id . '/' . $request->device_type);
                return response()->json([
                    'message' => 'Success',
                    'data' => $url,
                ], 200);
            }
            $code = Str::uuid();
            $payment = Payment::create([
                'user_id' => $order->user_id,
              //  'specialist_id' => $order->specialist_id,
                'order_id' => $order->id,
                'url' => $code,
                'status' => 'opened_link',
                'kind'=>'normal_order'
            ]);
            $url = url('payment/' . $request->order_id . '/' . $code . '/' . $order->user_id . '/' . $payment->id . '/' . $request->device_type);
            return response()->json([
                'message' => 'Success',
                'data' => $url,
            ], 200);
        }
        else{
            $order = OrderService::where('id', $request->order_id)->first();
            $currentTime = Carbon::now();
            if (!$order) {
                return response()->json([
                    'message' => 'Order Not Found',
                ], 400);
            }

            if ($order->status_payment == 'paid') {
                $url = url('successpayment');
                return response()->json([
                    'message' => 'success',
                    'data' => $url,
                ], 200);
            }
            $orderTime = Carbon::parse($order->created_at);
            if ($currentTime->diffInMinutes($orderTime) > 15) {
                $payment = Payment::where('kind','service')->where('order_id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
                if ($payment) {
                    $payment->delete();
                }
                $order->delete();
                return response()->json([
                    'message' => 'تم تجاوز الحد الاقصي للوقت المسموح للدفع برجاء الطلب  مرة اخري ',
                ], 400);
            }
            $payment = Payment::where('kind','service')->where('order_id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
            if ($payment) {
                $url = url('payment/' . $request->order_id . '/' . $payment->url . '/' . $order->user_id . '/' . $payment->id . '/' . $request->device_type);
                return response()->json([
                    'message' => 'Success',
                    'data' => $url,
                ], 200);
            }
            $code = Str::uuid();
            $payment = Payment::create([
                'user_id' => $order->user_id,
              //  'specialist_id' => $order->specialist_id,
                'order_id' => $order->id,
                'url' => $code,
                'status' => 'opened_link',
                'kind'=>'service'
            ]);
            $url = url('payment/' . $request->order_id . '/' . $code . '/' . $order->user_id . '/' . $payment->id . '/' . $request->device_type);
            return response()->json([
                'message' => 'Success',
                'data' => $url,
            ], 200);
        }

    }

    public function get_status_payment(Request $request)
    {
        if($request->type == 'order') {
            $order = Order::where('id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
            $payment = Payment::where('order_id', $request->order_id)->first();
            if ($order->status_payment == 'not_paid' && $payment->status == 'processing') {
                $is_paid = false;
                $status = 'processing';
            } elseif ($order->status_payment == 'not_paid' && $payment->status != 'processing' && $payment->status != 'failed') {
                $is_paid = false;
                $status = 'not_paid';
            } elseif ($order->status_payment == 'not_paid' && $payment->status == 'failed') {
                $is_paid = false;
                $status = 'failed';
            } else {
                $is_paid = true;
                $status = 'paid';
            }
            return response()->json([
                'message' => 'Success',
                'is_paid' => $is_paid,
                'status' => $status,
            ], 200);
        }
        elseif($request->type == 'normal'){
            $order = OrderNormal::where('id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
            $payment = Payment::where('kind','normal_order')->where('order_id', $request->order_id)->first();
            if ($order->status_payment == 'not_paid' && $payment->status == 'processing') {
                $is_paid = false;
                $status = 'processing';
            } elseif ($order->status_payment == 'not_paid' && $payment->status != 'processing' && $payment->status != 'failed') {
                $is_paid = false;
                $status = 'not_paid';
            } elseif ($order->status_payment == 'not_paid' && $payment->status == 'failed') {
                $is_paid = false;
                $status = 'failed';
            } else {
                $is_paid = true;
                $status = 'paid';
            }
            return response()->json([
                'message' => 'Success',
                'is_paid' => $is_paid,
                'status' => $status,
            ], 200);
        }
        else{
            $order = OrderService::where('id', $request->order_id)->where('user_id', Auth::guard('user-api')->user()->id)->first();
            $payment = Payment::where('kind','service')->where('order_id', $request->order_id)->first();
            if ($order->status_payment == 'not_paid' && $payment->status == 'processing') {
                $is_paid = false;
                $status = 'processing';
            } elseif ($order->status_payment == 'not_paid' && $payment->status != 'processing' && $payment->status != 'failed') {
                $is_paid = false;
                $status = 'not_paid';
            } elseif ($order->status_payment == 'not_paid' && $payment->status == 'failed') {
                $is_paid = false;
                $status = 'failed';
            } else {
                $is_paid = true;
                $status = 'paid';
            }
            return response()->json([
                'message' => 'Success',
                'is_paid' => $is_paid,
                'status' => $status,
            ], 200);
        }
    }
}
