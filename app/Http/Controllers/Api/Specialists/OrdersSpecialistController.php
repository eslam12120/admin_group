<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderNormal;
use App\Models\OrderNormalSpecialist;
use App\Models\OrderService;
use App\Models\RejectionReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class OrdersSpecialistController extends Controller
{
    //
    
    public function order_schadule(Request $request)
    {
        $orders = Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->Update
        ([
            'status' => 'schedule',
            'schedule_date'=>$request->schedule_date
        ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',
       

        ));
    }
    public function order_finished(Request $request)
    {
        $orders = Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->Update([
                'status' => 'finished ',
               
            ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function order_cancelled(Request $request)
    {
        $orders = Order::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('id', $request->order_id)->Update([
            'status' => 'cancelled ',

        ]);
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
    
        $orders = OrderNormal::where('id', $request->order_id)->Update([
            'status' => 'finished ',

        ]);
        $data = OrderNormalSpecialist::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('order_id', $request->order_id)->Update([
            'status' => 'finished ',

        ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function normal_order_cancelled(Request $request)
    {

        $orders = OrderNormal::where('id', $request->order_id)->Update([
            'status' => 'cancelled',

        ]);
        $data = OrderNormalSpecialist::where('specialist_id', Auth::guard('specialist-api')->user()->id)->where('order_id', $request->order_id)->Update([
            'status' => 'cancelled',

        ]);
        RejectionReason::create([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'type' => 'normal_order',

        ]);
        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
    public function service_order_finished(Request $request)
    {

        $orders = OrderService::where('id', $request->order_id)->Update([
            'specialist_id'=> Auth::guard('specialist-api')->user()->id,
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
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'status' => 'cancelled',

        ]);
        RejectionReason::create([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'type' => 'service_order',

        ]);

        return Response::json(array(
            'status' => 200,
            'message' => 'updated successfully',


        ));
    }
  

}
