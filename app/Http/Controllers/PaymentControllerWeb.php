<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderNormal;
use App\Models\OrderService;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;

class PaymentControllerWeb extends Controller
{
    private $mfConfig;
  //  private $database;
    public function __construct()
    {
      //  $this->database = \App\Services\Classes\FirebaseService::connect();
        $this->mfConfig = [
            'apiKey'      => config('myfatoorah.api_key'),
            'countryCode' => 'SAU',
            'isTest'      => false,
        ];
    }
    public function initiateSession($id, $code, $userid, $payment_id, $type)
    {
        $payment=Payment::where('id',$payment_id)->first();
        if($payment){
            if($payment->kind == 'order'){
                $order = Order::where('id', $id)->where('user_id', $userid)->first();
                $currentTime = Carbon::now();
                if (!$order) {
                    return view('404');
                }
                if ($order->status_payment == 'paid') {
                    return view('successpayment');
                }

                $payment = Payment::where('id', $payment_id)->where('user_id', $userid)->where('order_id', $id)->where('url', $code)->first();
                if (!$payment) {
                    return view('404');
                }
                $orderTime = Carbon::parse($order->created_at);
                if ($currentTime->diffInMinutes($orderTime) > 15) {
                    $payment = Payment::where('id', $payment_id)->where('order_id', $order->id)->where('user_id', $userid)->first();
                    if ($payment) {
                        $payment->delete();
                    }
                    $order->delete();
                    return view('404');
                }
                try {
                    if ($type == '0') {
                        $user = User::where('id', $order->user_id)->first();
                        $mfObj = new MyFatoorahPayment($this->mfConfig);
                        $postFields = [
                            'CustomerIdentifier' =>$order->user_id,
                            'SaveToken' => true,
                        ];
                        $data = $mfObj->InitiateSession($postFields);
                        return view('payment', compact('data', 'user', 'order','payment'));
                    } else {
                        $user = User::where('id', $order->user_id)->first();
                        // try {
                        $mfObj = new MyFatoorahPayment($this->mfConfig);
                        $postFields = [
                            'CustomerIdentifier' => $order->user_id,
                            'SaveToken' => true,
                        ];
                        $data = $mfObj->InitiateSession($postFields);
                        return view('payment_old_cards', compact('data', 'user', 'order','payment'));
                    }
                } catch (\Exception $ex) {
                    return back()->withErrors($ex->getMessage());
                }
            }elseif($payment->kind == 'normal_order'){
                $order = OrderNormal::where('id', $id)->where('user_id', $userid)->first();
                $currentTime = Carbon::now();
                if (!$order) {
                    return view('404');
                }
                if ($order->status_payment == 'paid') {
                    return view('successpayment');
                }

                $payment = Payment::where('id', $payment_id)->where('user_id', $userid)->where('order_id', $id)->where('url', $code)->first();
                if (!$payment) {
                    return view('404');
                }
                $orderTime = Carbon::parse($order->created_at);
                if ($currentTime->diffInMinutes($orderTime) > 15) {
                    $payment = Payment::where('id', $payment_id)->where('order_id', $order->id)->where('user_id', $userid)->first();
                    if ($payment) {
                        $payment->delete();
                    }
                    $order->delete();
                    return view('404');
                }
                try {
                    if ($type == '0') {
                        $user = User::where('id', $order->user_id)->first();
                        $mfObj = new MyFatoorahPayment($this->mfConfig);
                        $postFields = [
                            'CustomerIdentifier' => $order->user_id,
                            'SaveToken' => true,
                        ];
                        $data = $mfObj->InitiateSession($postFields);
                        return view('payment', compact('data', 'user', 'order','payment'));
                    } else {
                        $user = User::where('id', $order->user_id)->first();
                        // try {
                        $mfObj = new MyFatoorahPayment($this->mfConfig);
                        $postFields = [
                            'CustomerIdentifier' => $order->user_id,
                            'SaveToken' => true,
                        ];
                        $data = $mfObj->InitiateSession($postFields);
                        return view('payment_old_cards', compact('data', 'user', 'order','payment'));
                    }
                } catch (\Exception $ex) {
                    return back()->withErrors($ex->getMessage());
                }
            }
            else{
                $order = OrderService::where('id', $id)->where('user_id', $userid)->first();
                $currentTime = Carbon::now();
                if (!$order) {
                    return view('404');
                }
                if ($order->status_payment == 'paid') {
                    return view('successpayment');
                }

                $payment = Payment::where('id', $payment_id)->where('user_id', $userid)->where('order_id', $id)->where('url', $code)->first();
                if (!$payment) {
                    return view('404');
                }
                $orderTime = Carbon::parse($order->created_at);
                if ($currentTime->diffInMinutes($orderTime) > 15) {
                    $payment = Payment::where('id', $payment_id)->where('order_id', $order->id)->where('user_id', $userid)->first();
                    if ($payment) {
                        $payment->delete();
                    }
                    $order->delete();
                    return view('404');
                }
                try {
                    if ($type == '0') {
                        $user = User::where('id', $order->user_id)->first();
                        $mfObj = new MyFatoorahPayment($this->mfConfig);
                        $postFields = [
                            'CustomerIdentifier' =>$order->user_id,
                            'SaveToken' => true,
                        ];
                        $data = $mfObj->InitiateSession($postFields);
                        return view('payment', compact('data', 'user', 'order','payment_id'));
                    } else {
                        $user = User::where('id', $order->user_id)->first();
                        // try {
                        $mfObj = new MyFatoorahPayment($this->mfConfig);
                        $postFields = [
                            'CustomerIdentifier' => $order->user_id,
                            'SaveToken' => true,
                        ];
                        $data = $mfObj->InitiateSession($postFields);
                        return view('payment_old_cards', compact('data', 'user', 'order','payment'));
                    }
                } catch (\Exception $ex) {
                    return back()->withErrors($ex->getMessage());
                }
            }
        }
        else {
            return view('404');
        }
    }
    public function updateSession(Request $request)
    {
        try {
            $client = new Client();
            $response = $client->post('https://api-sa.myfatoorah.com/v2/UpdateSession', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->mfConfig['apiKey'],
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'SessionId'    => $request->input('session_id'),
                    'Token'        => $request->input('token'),
                    'TokenType'    => 'mftoken',
                    'SecurityCode' => $request->input('cvv'),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents());

            // Check if the response contains the SessionId
            if (isset($data->Data->SessionId)) {
                $sessionId = $data->Data->SessionId;
            } else {
                // Handle missing SessionId
                return back()->withErrors('SessionId not found in the response');
            }

            // Proceed with the redirect
            return $this->executePayment($request->merge([
                'session_id' => $sessionId,
                'order_id' =>  $request->input('order_id'),
                'card_brand' => $request->input('card_brand'),
            ]));
        } catch (\Exception $ex) {
            return back()->withErrors($ex->getMessage());
        }
    }
    public function executePayment(Request $request)
    {
        if($request->input('kind') == 'order') {
            $order = Order::where('id', $request->input('order_id'))->first();
            if (!$order) {
                return view('404');
            }
            if ($order->status_payment == 'paid') {
                return view('successpayment');
            }
            $encryptedOrderId = encrypt($request->input('order_id'));
            try {
                $mfObj = new MyFatoorahPayment($this->mfConfig);
                $postFields = [
                    'SessionId' => $request->input('session_id'),
                    'InvoiceValue' => $request->input('invoice_value'),
                    "CustomerName" => $request->input('name'),
                    "Notificationoption" => "LNK",
                    "DisplayCurrencyIso" => "SAR",
                    "DisplayCurrencyIna" => "SAR",
                    // "CustomerEmail" => $request->input('email'),
                    "CallBackUrl" => url('success/payment/' . $encryptedOrderId.'/'.$request->input('kind')),
                    "ErrorUrl" => url('failed/payment/' . $encryptedOrderId.'/'.$request->input('kind')),
                    "Language" => "ar",
                    "CustomerReference" => $request->input('payment_id'),
                ];
                $data = $mfObj->executePayment($postFields);
                if (isset($data->InvoiceId) && $data->InvoiceId !== null) {
                    // Process the invoice ID (e.g., save it to the database, log it, etc.)
                    Payment::where('kind', 'order')->where('order_id', $request->input('order_id'))->update([
                        'invoice_id' => $data->InvoiceId,
                        'status' => 'processing',
                        'card_type' => $request->input('card_brand')
                    ]);
                } else {
                    Payment::where('kind', 'order')->where('order_id', $request->input('order_id'))->update([
                        'invoice_id' => null,
                        'status' => 'processing',
                        'card_type' => $request->input('card_brand')
                    ]);
                }
                return redirect()->away($data->PaymentURL);
            } catch (\Exception $ex) {
                return back()->withErrors($ex->getMessage());
            }
        }
        elseif($request->input('kind') == 'normal_order'){
            $order = OrderNormal::where('id', $request->input('order_id'))->first();
            if (!$order) {
                return view('404');
            }
            if ($order->status_payment == 'paid') {
                return view('successpayment');
            }
            $encryptedOrderId = encrypt($request->input('order_id'));
            try {
                $mfObj = new MyFatoorahPayment($this->mfConfig);
                $postFields = [
                    'SessionId' => $request->input('session_id'),
                    'InvoiceValue' => $request->input('invoice_value'),
                    "CustomerName" => $request->input('name'),
                    "Notificationoption" => "LNK",
                    "DisplayCurrencyIso" => "SAR",
                    "DisplayCurrencyIna" => "SAR",
                    // "CustomerEmail" => $request->input('email'),
                    "CallBackUrl" => url('success/payment/' . $encryptedOrderId.'/'.$request->input('kind')),
                    "ErrorUrl" => url('failed/payment/' . $encryptedOrderId.'/'.$request->input('kind')),
                    "Language" => "ar",
                    "CustomerReference" => $request->input('payment_id'),
                ];
                $data = $mfObj->executePayment($postFields);
                if (isset($data->InvoiceId) && $data->InvoiceId !== null) {
                    // Process the invoice ID (e.g., save it to the database, log it, etc.)
                    Payment::where('kind', 'normal_order')->where('order_id', $request->input('order_id'))->update([
                        'invoice_id' => $data->InvoiceId,
                        'status' => 'processing',
                        'card_type' => $request->input('card_brand')
                    ]);
                } else {
                    Payment::where('kind', 'normal_order')->where('order_id', $request->input('order_id'))->update([
                        'invoice_id' => null,
                        'status' => 'processing',
                        'card_type' => $request->input('card_brand')
                    ]);
                }
                return redirect()->away($data->PaymentURL);
            } catch (\Exception $ex) {
                return back()->withErrors($ex->getMessage());
            }
            }
        else{
            $order = OrderService::where('id', $request->input('order_id'))->first();
            if (!$order) {
                return view('404');
            }
            if ($order->status_payment == 'paid') {
                return view('successpayment');
            }
            $encryptedOrderId = encrypt($request->input('order_id'));
            try {
                $mfObj = new MyFatoorahPayment($this->mfConfig);
                $postFields = [
                    'SessionId' => $request->input('session_id'),
                    'InvoiceValue' => $request->input('invoice_value'),
                    "CustomerName" => $request->input('name'),
                    "Notificationoption" => "LNK",
                    "DisplayCurrencyIso" => "SAR",
                    "DisplayCurrencyIna" => "SAR",
                    // "CustomerEmail" => $request->input('email'),
                    "CallBackUrl" => url('success/payment/' . $encryptedOrderId.'/'.$request->input('kind')),
                    "ErrorUrl" => url('failed/payment/' . $encryptedOrderId.'/'.$request->input('kind')),
                    "Language" => "ar",
                    "CustomerReference" => $request->input('payment_id'),
                ];
                $data = $mfObj->executePayment($postFields);
                if (isset($data->InvoiceId) && $data->InvoiceId !== null) {
                    // Process the invoice ID (e.g., save it to the database, log it, etc.)
                    Payment::where('kind', 'service')->where('order_id', $request->input('order_id'))->update([
                        'invoice_id' => $data->InvoiceId,
                        'status' => 'processing',
                        'card_type' => $request->input('card_brand')
                    ]);
                } else {
                    Payment::where('kind', 'service')->where('order_id', $request->input('order_id'))->update([
                        'invoice_id' => null,
                        'status' => 'processing',
                        'card_type' => $request->input('card_brand')
                    ]);
                }
                return redirect()->away($data->PaymentURL);
            } catch (\Exception $ex) {
                return back()->withErrors($ex->getMessage());
            }
        }
    }
    public function callback_url($encryptedOrderId ,$kind)
    {
        if($kind == 'order') {
            try {
                $order_id = decrypt($encryptedOrderId);
            } catch (\Exception $e) {
                $order_id = decrypt($encryptedOrderId);
                Payment::where('kind', 'order')->where('order_id', $order_id)->update([
                    'status' => 'failed',
                    'message' => 'Failed.',
                ]);
                return view('404');
            }
            $order = Order::where('id', $order_id)->first();
            $order->update([
                'status_payment' => 'paid',
            ]);
            Payment::where('kind', 'order')->where('order_id', $order_id)->update([
                'status' => 'paid',
                'message' => 'success',
            ]);
            return view('successpayment');
        }
        elseif($kind == 'normal_order'){
            try {
                $order_id = decrypt($encryptedOrderId);
            } catch (\Exception $e) {
                $order_id = decrypt($encryptedOrderId);
                Payment::where('kind', 'normal_order')->where('order_id', $order_id)->update([
                    'status' => 'failed',
                    'message' => 'Failed.',
                ]);
                return view('404');
            }
            $order = OrderNormal::where('id', $order_id)->first();
            $order->update([
                'status_payment' => 'paid',
            ]);
            Payment::where('kind', 'normal_order')->where('order_id', $order_id)->update([
                'status' => 'paid',
                'message' => 'success',
            ]);
            return view('successpayment');
        }
        else{
            try {
                $order_id = decrypt($encryptedOrderId);
            } catch (\Exception $e) {
                $order_id = decrypt($encryptedOrderId);
                Payment::where('kind', 'service')->where('order_id', $order_id)->update([
                    'status' => 'failed',
                    'message' => 'Failed.',
                ]);
                return view('404');
            }
            $order = OrderService::where('id', $order_id)->first();
            $order->update([
                'status_payment' => 'paid',
            ]);
            Payment::where('kind', 'service')->where('order_id', $order_id)->update([
                'status' => 'paid',
                'message' => 'success',
            ]);
            return view('successpayment');
        }
    }

    public function failed_url($encryptedOrderId,$kind)
    {
        if($kind == 'order') {
            try {
                $order_id = decrypt($encryptedOrderId);
            } catch (\Exception $e) {
                $order_id = decrypt($encryptedOrderId);
                Payment::where('kind', 'order')->where('order_id', $order_id)->update([
                    'status' => 'failed',
                    'message' => 'Failed.',
                ]);
                return view('404');
            }
            $order = Order::where('id', $order_id)->first();
            $order->update([
                'status_payment' => 'not_paid',
            ]);
            Payment::where('kind', 'order')->where('order_id', $order_id)->update([
                'status' => 'not_paid',
                'message' => 'failed',
            ]);
            return view('failedpayment');
        }
        elseif($kind == 'normal_order'){
            try {
                $order_id = decrypt($encryptedOrderId);
            } catch (\Exception $e) {
                $order_id = decrypt($encryptedOrderId);
                Payment::where('kind', 'normal_order')->where('order_id', $order_id)->update([
                    'status' => 'failed',
                    'message' => 'Failed.',
                ]);
                return view('404');
            }
            $order = OrderNormal::where('id', $order_id)->first();
            $order->update([
                'status_payment' => 'not_paid',
            ]);
            Payment::where('kind', 'normal_order')->where('order_id', $order_id)->update([
                'status' => 'not_paid',
                'message' => 'failed',
            ]);
            return view('failedpayment');
        }
        else{
            try {
                $order_id = decrypt($encryptedOrderId);
            } catch (\Exception $e) {
                $order_id = decrypt($encryptedOrderId);
                Payment::where('kind', 'service')->where('order_id', $order_id)->update([
                    'status' => 'failed',
                    'message' => 'Failed.',
                ]);
                return view('404');
            }
            $order = OrderService::where('id', $order_id)->first();
            $order->update([
                'status_payment' => 'not_paid',
            ]);
            Payment::where('kind', 'service')->where('order_id', $order_id)->update([
                'status' => 'not_paid',
                'message' => 'failed',
            ]);
            return view('failedpayment');
        }


    }
/*
    public function makeRefund($invoice_id, $amount)
    {
        // $config = config('myfatoorah');
        $mfObj = new MyFatoorahRefund($this->mfConfig);
        $postFields = [
            'KeyType' => 'invoiceid',
            'Key'     => $invoice_id,
            'Amount'  => $amount, // Can be a full or partial refund
        ];

        //  try {
        $data = $mfObj->makeRefund($postFields);

        return response()->json([
            'status'  => 'success',
            'refund_id' => $data->RefundId,
            'refund_reference' => $data->RefundReference,
            'data'    => $data,
        ]);
        // } catch (\Exception $ex) {
        //   return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 400);
        // }
    }

    public function getRefundStatus(Request $request)
    {
        // $config = config('myfatoorah');
        $mfObj = new MyFatoorahPayment($this->mfConfig);
        $postFields = [
            'KeyType' => $request->input('key_type'),
            'Key'     => $request->input('key_id'),
        ];
        try {
            //  $apiURL = $mfObj->getApiUrl();
            $obj    = $mfObj->callAPI("https://apitest.myfatoorah.com/v2/GetRefundStatus", $postFields);
            return response()->json([
                'status' => 'success',
                'refund_status' => $obj->Data->RefundStatusResult[0]->RefundStatus,
                'data'   => $obj->Data,
            ]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 400);
        }
    }*/

    public function success_url()
    {
        return view('successpayment');
    }

    //new

    public function select_payment($id, $code, $userid, $payment_id, $device_type)
    {
        return view('selectionpayment', compact('id', 'code', 'userid', 'payment_id', 'device_type'));
    }

}
