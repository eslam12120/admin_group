<?php

namespace App\Http\Controllers\Api\Specialists;

use App\Http\Controllers\Controller;
use App\Models\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HomeSpecialistController extends Controller
{
    //
    public function getdata()
    {
        $data=OrderService::where('status','pendding')->with(
            [
                'service_special' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name');
                }])->get();
        return Response::json(array(
            'status' => 200,
            'message' => 'true',
            'data' => $data,
        ));
    }
}
