<?php

namespace App\Services;

use Midtrans\Midtrans;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapToken(array $data)
    {

        $item_details = $data['products'];

        $params = array(
            'transaction_details' => array(
                'order_id' => $data['order_id'],
                'gross_amount' => $data['amount'],
            ),
            'customer_details' => array(
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ),
            'item_details' =>  $item_details,
        );

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }

    public function handleNotification($notif)
    {
        return $notif;
    }
}
