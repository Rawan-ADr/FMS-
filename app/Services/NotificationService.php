<?php

namespace App\Services;

use App\Models\Notification ;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationService
{

    public function index()
    {
        return auth()->user()->notifications;
    }


    public function send($user, $title, $message, $type = 'basic')
    {

       
        // Path to the service account key JSON file
        $serviceAccountPath = storage_path('internetapp2025-firebase-adminsdk-fbsvc-f46cba838b.json');

        // Initialize the Firebase Factory with the service account
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);

        // Create the Messaging instance
        $messaging = $factory->createMessaging();

        // Prepare the notification array
        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
            
        ];
          $data = [
            'type' => $type,
            'id' =>$user->id,
            'message' => $message,
        ];
         // Save the notification to the database
      
        // Create the CloudMessage instance
        $cloudMessage = CloudMessage::withTarget('token', $user->fcm_token)
            ->withNotification($notification)->withData($data);

         try {
            // Send the notification
            $messaging->send($cloudMessage);

           

           
            // return 1;


        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error($e->getMessage());
            return 0;
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::error($e->getMessage());
            return 0;
        }
    }
    public function saveN($user, $title, $message){
              $noti= Notification::query()->create([
                
                    'user_id' => $user->id,
                    'message' => $message,
                    'title' => $title,
                    // The data of the notification
            ]);
            $noti->save();
    }

    











    public function markAsRead($notificationId): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);

        if(isset($notification)) {
            $notification->markAsRead();
            return true;
        }else return false;
    }

    public function destroy($id): bool
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        if(isset($notification)) {
            $notification->delete();
            return true;
        }else return false;
    }

}
