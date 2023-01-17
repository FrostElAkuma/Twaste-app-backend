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
            $status = $order-order_status;

            //Checking the kind of status for the order before sending notification
            $value = self::order_status_update_message($status);

            //If not null, not zero, etc.
            if($value) {
                
                //data of our notification. Image and type not really important
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order-id,
                    'image' => '',
                    'type' => 'order_status',
                ];

                //This is the function that actually send the notification to our device
                //Also Note we used self:: because the send_push_notif... function is in the same file
                self::send_push_notif_to_device($token, $data);

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
        }

        //The url where we will send the message to the firebase server. It is the endpoint
        $url = "https//fcm.googleapis.come/fcm/send";
        //Have no idea what the below line does and what is that syntax
        //The content we are passing is the key that we got from firebase. This header is the info that the api needs
        $header = array("authorization: key=" . $key['content'] . "",);

        //I need to look into the "'..'" syntax (I think it is php json syntax)
        //Note reminder the fcm token is the user device token
        //This is the data that we will send over to google firebase servers
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

        //Need to look into these as well the CURL
        //These are the options that Google needs to send the info to google fire base server. 
        $ch = curl_init();
        $timeout = 120;
        curl__setopt($ch, CURLOPT_URL, $url);
        curl__setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl__setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl__setopt($ch, CURLOPT_CUSTOMERREUEST, "POST");
        curl__setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl__setopt($ch, CURLOPT_HTTPHEADER, $header);

        //Get URL content and checking the results of us trying to send the data
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

        return $data['value']['message'];
    }
}