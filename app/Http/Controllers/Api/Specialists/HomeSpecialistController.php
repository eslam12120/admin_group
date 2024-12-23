<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Http\Controllers\Controller;
use App\Models\OrderFile;
use App\Models\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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

}
