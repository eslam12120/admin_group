<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function __construct()
    {
        $this->FirebaseUri    =  env('FirebaseUri') ;
    }

    public function send(array $data,$token)
    {
        $serviceAccount = ServiceAccount::fromValue(__DIR__.'/FireBaseJson.json');

        $deviceToken = $token;

        $messaging = (new Firebase\Factory())
                    ->withServiceAccount($serviceAccount)
                    ->withDatabaseUri($this->FirebaseUri)
                    ->createMessaging();

        $message = CloudMessage::fromArray([
            'token' => $deviceToken,
            'notification' => $data, // optional
            'data' => $data, // optional
        ]);

        $messaging->send($message);

        return json_encode($message);
    }
}
