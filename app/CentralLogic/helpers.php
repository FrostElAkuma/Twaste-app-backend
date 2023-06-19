<?php

namespace App\CentralLogics;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
//So I can use the business_settings from my DB. That BusinessSetting is a model that we created
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function get_business_settings($name)
    {
        $config = null;

        $paymentmethod = BusinessSetting::where('key', $name)->first();

        if ($paymentmethod) {

            $config = json_decode(json_encode($paymentmethod->value), true);
            $config = json_decode($config, true);
        }

        return $config;
    }
    
    public static function send_order_notification($order, $token){
        
        try {
            $status = $order->order_status;

            //Checking the kind of status for the order before sending notification
            //error_log('I am inside the notification proccess 1 ');
            //error_log(print_r($status,true));
            $value = self::order_status_update_message($status);
            //error_log('I am inside the notification proccess 2');

            //If not null, not zero, etc.
            if(true) {
                //error_log('I am inside the notification proccess 3');
                //data of our notification. Image and type not really important
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => "aaa finally working",
                    'order_id' => $order->id,
                    'image' => '',
                    'type' => 'order_status',
                ];

                //This is the function that actually send the notification to our device
                //Also Note we used self:: because the send_push_notif... function is in the same file
                ///error_log('I am inside the notification proccess 4');
                //error_log(print_r($token,true));
                self::send_push_notif_to_device($token, $data);
                //error_log('I am inside the notification proccess 5');
                //Saving notifications sent to a certain user in our DB
                try{
                    DB::table('user_notifications')->insert([
                        'data'=>json_encode($data),
                        'user_id'=>$order->user_id,
                        'created_at'=>now(),
                        'updated_at'=>now(),
                    ]); 
                } catch (\Exception $e) {
                    return response()->json([$e], 403);
                }
            }

            return true;
        } catch (\Exception $e) {
            info($e);
        }
        return false;
    }

    //Sennding the notification
    public static function send_push_notif_to_device($fcm_token, $data, $delivery=0)
    {
        $key=0;
        //This is getting the notification server key (we need to get this from firebase inside project settingsf -> cloud messaging and then put it in our DB inside BusinessSetting)
        if($delivery==1){
            $key = BusinessSetting::where(['key' => 'delivery_boy_push_notification_key'])->first()->value;
        }
        else {
            $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
            //Ok so I am getting the key correctly
            //error_log('I am inside helpers line 96');
            //error_log(print_r($key,true));
        }

        //The url where we will send the message to the firebase server. It is the endpoint
        $url = "https://fcm.googleapis.com/fcm/send";
        //Have no idea what the below line does and what is that syntax
        //The content we are passing is the key that we got from firebase. This header is the info that the api needs
        //I can check in postman as well
        //error_log('I am inside helpers line 107');
        /*$header = array("authorization: key=" . $key['content'] . "",
            "content-type: application/json"
        );*/
        $header = [
            'Authorization: key=' . $key,
            'Content-Type: application/json',
        ];

        //I need to look into the "'..'" syntax (I think it is php json syntax)
        //Note reminder the fcm token is the user device token
        //This is the data that we will send over to google firebase servers
        //error_log('I am inside helpers line 114');
        $postdata = '{
            "to" : "' . $fcm_token . '",
            "mutable_content": true,
            "data" : {
                "title":"' . $data['title'] . '"
                "body": "' . $data['description'] . '",
                "order_id": "' . $data['order_id'] . '",
                "type": "' . $data['type'] . '",
                "is_read": 0,
            },
            "notification" : {
                "title" : "' . $data['title'] . '",
                "body": "' . $data['description'] . '",
                "order_id": "' . $data['order_id'] . '",
                "title_loc_key": "' . $data['order_id'] . '", 
                "body_loc_key": "' . $data['type'] . '", 
                "type": "' . $data['type'] . '",
                "is_read": 0,
                "icon": "new",
                "android_channel_id": "idkForNow?",
            }
        }';

        //error_log(print_r($postdata,true));

        //error_log('I am inside helpers line 142');
        //Need to look into these as well the CURL
        //These are the options that Google needs to send the info to google fire base server. 
        $ch = curl_init();
        //error_log('I am inside helpers line 149');
        $timeout = 120;
        //error_log('I am inside helpers line 151');
        //Had a typo here. Had 2 underscores :')
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //error_log('I am inside helpers line 153');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //error_log('I am inside helpers line 158');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        //error_log('I am inside helpers line 160');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //error_log('I am inside helpers line 162');

        //Get URL content and checking the results of us trying to send the data
        //error_log('I am inside helpers line 165');
        $result = curl_exec($ch);
        if($result === FALSE) {
            dd( curl_error($ch));
        }

        curl_close($ch);
        //We not using this result for now
        return $result;
    }

    //Getting the message from our DB BusinessSetting table
    public static function order_status_update_message($status)
    {
        //Each of these messages are inside our Db as Json format
        if($status == 'pending'){
            $data = BusinessSetting::where('key', 'order_pending_message')->first();
            //error_log('hehehe I am ehreere');
            //error_log(print_r($data,true));
            return "order pending";
        }
        elseif ($status == 'confirmed') {
            $data = BusinessSetting::where('key', 'order_confirmation_message')->first();
        }
        elseif ($status == 'processing') {
            $data = BusinessSetting::where('key', 'order_processing_message')->first();
        }
        elseif ($status == 'picked_up') {
            $data = BusinessSetting::where('key', 'out_for_delivery_message')->first();
        }
        elseif ($status == 'handover') {
            $data = BusinessSetting::where('key', 'order_handover_message')->first();
        }
        elseif ($status == 'delivered') {
            $data = BusinessSetting::where('key', 'order_delivered_message')->first();
        }
        elseif ($status == 'delivery_boy_delivered') {
            $data = BusinessSetting::where('key', 'delivery_boy_delivered_message')->first();
        }
        elseif ($status == 'accepted') {
            $data = BusinessSetting::where('key', 'delivery_boy_assign_message')->first();
        }
        elseif ($status == 'canceled') {
            $data = BusinessSetting::where('key', 'order_canceled_message')->first();
        }
        elseif ($status == 'refunded') {
            $data = BusinessSetting::where('key', 'order_refunded_message')->first();
        }
        else {
            $data = '{"status":"0","message":""}';
        }
        //error_log('hehehe I am ohohohohohohoh');
        return $data['value']['message'];
    }
}