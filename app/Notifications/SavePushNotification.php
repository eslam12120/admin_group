<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Factory;

class SavePushNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;

    protected $sound;
    protected $firebaseMessaging;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;

        $this->firebaseMessaging = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->createMessaging();
    }

    public function via($notifiable)
    {
        return ['firebase'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
        ];
    }
    public function toFirebase($notifiable)
    {
        // Fetch the device token from the database
        $userDeviceToken = DB::table('users')
            ->where('device_token', $notifiable)  // Assuming $notifiable->id is the user ID
            ->first();

        if (!$userDeviceToken) {
            throw new \Exception("No device token available");
        }

        // Create the notification
        $notification = FirebaseNotification::create($this->title, $this->message);

        // Create the data payload
        $dataPayload = [
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'status' => 'done',
            'title' => $this->title,
            'message' => $this->message,
            'priority' =>  'high',
        ];

        // Create the CloudMessage
        $message = CloudMessage::withTarget('token', $notifiable)
            ->withNotification($notification)
            ->withData($dataPayload);

        return $this->firebaseMessaging->send($message);
    }
}
