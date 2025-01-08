<?php

namespace App\Http\Controllers\AdminApi;

use App\Models\User;
use App\Models\Order;
use App\Models\Specialist;
use App\Models\OrderNormal;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;

class AdminOrdersController extends Controller
{
    public function orders(){
        $orders = Order::with('specialist.special_order','user') // Include specialist and special
        ->paginate(30);

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
    public function normal(){
        $orders = OrderNormal::with(['ordernormal', 'orderfiles','user'])
        ->paginate(30);

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
    public function order_service(){
        $orders = OrderService::with(['specialist.special_order', 'orderfiles','user']) // Include 'special' relation
        ->paginate(30);
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


    public function counts(Request $request)
{
    // Count active specialists
    $sp = Specialist::where('status', '1')->count();

    // Count all users
    $user = User::count();

    // Count all orders from different types
    $orders_1 = Order::count();
    $orders_2 = OrderNormal::count();
    $orders_3 = OrderService::count();
    $count_orders = $orders_1 + $orders_2 + $orders_3;

    // Count online specialists and map image URLs
    $specialists_online = Specialist::where('status', '1')
        ->where('is_active', '1')
        ->select('id', 'image')
        ->get()
        ->map(function ($specialist) {
            $specialist->image_url = asset('specialist_images/' . $specialist->image);
            return $specialist;
        });

    // Count orders based on status and date
    $orders_f = OrderNormal::whereDate('created_at', $request->date)
        ->where('status', 'finished')
        ->count();
    $orders_p = OrderNormal::whereDate('created_at', $request->date)
        ->where('status', 'pending')
        ->count();
    $orders_c = OrderNormal::whereDate('created_at', $request->date)
        ->where('status', 'cancelled')
        ->count();

    // Fetch the latest notifications with user data
    $not = Notification::with('user')->latest()->take(5)->get();

    // Return response with all collected data
    return response()->json([
        'message' => 'success',
        'specialists_count' => $sp,
        'users_count' => $user,
        'total_orders_count' => $count_orders,
        'specialists_online' => $specialists_online,
        'orders_finished_count' => $orders_f,
        'orders_pending_count' => $orders_p,
        'orders_cancelled_count' => $orders_c,
        'latest_notifications' => $not,
    ], 200);
}

}
