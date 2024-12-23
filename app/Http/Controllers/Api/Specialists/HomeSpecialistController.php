<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Http\Controllers\Controller;
use App\Models\Negotation;
use App\Models\OrderFile;
use App\Models\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class HomeSpecialistController extends Controller
{
    //
    public function getdata()
    {
        $data=OrderService::where('status','pending')->with(
            [
                'service_special' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name');
                }])->get();
        $data->map(function ($data) {

            $data['files'] = OrderFile::where('order_id', $data['id'])->get();
        });
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $data,
        ));
    }
    public function get_data_by_id(Request $request)
    {
        $data = OrderService::where('id',$request->id)->with(
            [
                'service_special' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name');
                }
            ]
        )->get();
        $data->map(function ($data) {

            $data['files'] = OrderFile::where('order_id', $data['id'])->get();
        });
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $data,
        ));


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


        // Create the order using the validated data
        Negotation::create([
            'order_id' => $request->order_id,
            'user_id' => $request->user_id,
            'specialist_id' => Auth::guard('specialist-api')->user()->id,
            'time' => $request->time,
            'price' => $request->price,
            'status' => $request->status,
        ]);

        return Response::json(array(
            'status' => 200,
            'message' => 'created successfully',
          
        ));
    }

}
