<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Models\User;
use App\Models\Order;
use App\Models\Specialist;
use App\Models\OrderNormal;
use App\Models\Notification;
use App\Models\OrderService;
use Illuminate\Http\Request;
use App\Models\RejectionReason;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderNormalSpecialist;
use Illuminate\Support\Facades\Response;
use App\Notifications\SavePushNotification;
use App\Notifications\SavePushNotificationSpecialist;

class OrdersSpecialistController extends Controller
{
    //

    public function order_schadule(Request $request)
    {
        $orders = Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->update([
            'status' => 'schedule',
            'schedule_date'=>$request->schedule_date
        ]);
        $order=Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->first();
        try {
            $this->send_notify_user($order->user_id);
          //  $this->send_notify_provider($request->specialist_id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function order_finished(Request $request)
    {
        $orders = Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->update([
                'status' => 'finished ',

            ]);
            $order=Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->first();
            try {
                $this->send_notify_user($order->user_id);
              //  $this->send_notify_provider($request->specialist_id);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
            }
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function order_cancelled(Request $request)
    {
        $orders = Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->update([
            'status' => 'cancelled ',

        ]);
        $order=Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->first();
        try {
            $this->send_notify_user($order->user_id);
          //  $this->send_notify_provider($request->specialist_id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
    RejectionReason::create([
        'order_id'=> $request->order_id,
        'description'=>$request->description,
        'specialist_id'=> Auth::guard('specialist-api')->user()->id,
        'type'=>'order',

    ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function normal_order_finished(Request $request)
    {

        $orders = OrderNormal::where('id', $request->order_id)->update([
            'status' => 'finished ',

        ]);
        $data = OrderNormalSpecialist::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('order_id', $request->order_id)->update([
            'status' => 'finished ',

        ]);
        $order=OrderNormal::where('id', $request->order_id)->first();
        try {
            $this->send_notify_user($order->user_id);
          //  $this->send_notify_provider($request->specialist_id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function normal_order_cancelled(Request $request)
    {

        $orders = OrderNormal::where('id', $request->order_id)->update([
            'status' => 'cancelled',

        ]);
        $data = OrderNormalSpecialist::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('order_id', $request->order_id)->update([
            'status' => 'cancelled',

        ]);
        RejectionReason::create([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'type' => 'normal_order',

        ]);
        $order=OrderNormal::where('id', $request->order_id)->first();
        try {
            $this->send_notify_user($order->user_id);
          //  $this->send_notify_provider($request->specialist_id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function service_order_finished(Request $request)
    {

        $orders = OrderService::where('id', $request->order_id)->update([
            'specialist_id'=> Auth::guard('specialist-api')->user()->id,
            'status' => 'finished ',

        ]);

        $order=OrderService::where('id', $request->order_id)->first();
        try {
            $this->send_notify_user($order->user_id);
          //  $this->send_notify_provider($request->specialist_id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }


        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function service_order_cancelled(Request $request)
    {

        $orders = OrderService::where('id', $request->order_id)->update([
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'status' => 'cancelled',

        ]);
        RejectionReason::create([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'type' => 'service_order',

        ]);

        $order=OrderService::where('id', $request->order_id)->first();
        try {
            $this->send_notify_user($order->user_id);
          //  $this->send_notify_provider($request->specialist_id);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }


    public function send_notify_user($user_id)
    {
        $user = User::where('id', $user_id)->first();
        if ($user) {
            try {
                $title = "حالةة الطلب  ";
                $message = "تم  تعديل حالة طلبك قم بمراجعتها ";
                $this->save_notify_place($title, $message,  $user_id);
                if ($user->device_token) {
                    (new SavePushNotification($title, $message))->toFirebase($user->device_token);
                    return 1;
                } else {
                    return 0;
                }
            } catch (\Exception $e) {
                return 2;
            }
        } else {
            return 0;
        }
    }
    public function send_notify_provider($provider_id)
    {
        $user = Specialist::where('id', $provider_id)->first();
        if ($user) {
            try {
                $title = "طلب جديد";
                $message = "تم استقبال  طلبك بنجاح ";
                $this->save_notify_place($title, $message,  $provider_id);
                if ($user->device_token) {
                    (new SavePushNotificationSpecialist($title, $message))->toFirebase($user->device_token);
                    return 1;
                } else {
                    return 0;
                }
            } catch (\Exception $e) {
                return 2;
            }
        } else {
            return 0;
        }
    }
    public function send_notify_provider_stat($provider_id)
    {
        $user = Specialist::where('id', $provider_id)->first();
        if ($user) {
            try {
                $title = "حالة الطلب  ";
                $message = "تم تعديل حالة   طلبك  قم بمراجعتتها  ";
                $this->save_notify_place($title, $message,  $provider_id);
                if ($user->device_token) {
                    (new SavePushNotificationSpecialist($title, $message))->toFirebase($user->device_token);
                    return 1;
                } else {
                    return 0;
                }
            } catch (\Exception $e) {
                return 2;
            }
        } else {
            return 0;
        }
    }

    public function save_notify_place($title, $message, $user_id)
    {
        Notification::create([
            'title' => $title,
            'message' => $message,
            'receiver_id' => $user_id,
            'specialist_id' => null,
            'is_read' => '0',
            'sender_id' => 0,
        ]);
        /*
        DB::table('tbl_place_notification_orders')->insert([
            'title' => $title,
            'message' => $message,
            'receiver_id' => $place_id,
            'sender_type' => 'App\Models\User',
            'is_read' => '0',
            'sender_id' => $user_id,
            'place_id' => $place_id
        ]);
        return 1;
        */
        return 1;
    }

}
