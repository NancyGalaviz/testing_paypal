<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $tokens;

    public function __construct(Request $request)
    {
        $session = $request->session();
        $tokens = $session->get('tokens');
        $this->tokens = $tokens;
    }

    public function curl_json($data)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $data);
        $response = curl_exec($ch);
        if (!$response) {
            \Log::info('Failed: ' . curl_error($ch));
        }
        curl_close($ch);
        $jsonDecoded = json_decode($response, true);
        return $jsonDecoded;
    }

    public function get_status_payment($payment_data)
    {
        $amount_data = $this->curl_json([
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => 'https://api.sandbox.paypal.com/v1/payments/payment/' . $payment_data['id'],
            CURLOPT_HTTPHEADER     => ['Content-Type:application/json', 'Authorization:Bearer ' . $this->tokens['access_token']]
        ]);

        return $amount_data;
    }

    public function pay_execute($payment_data)
    {
        $data = ['payer_id' => $payment_data['payer_id']];
        $amount_data = $this->curl_json([
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_URL            => 'https://api.sandbox.paypal.com/v1/payments/payment/' . $payment_data['id']. '/execute',
            CURLOPT_HTTPHEADER     => ['Content-Type:application/json', 'Authorization:Bearer ' . $this->tokens['access_token']]
        ]);

        return $amount_data;
    }

    public function get_balance()
    {
        $fields = $this->encodeFields(
            array(
                'METHOD'    => 'GetBalance',
                'VERSION'   => '51.0',
                'USER'      => env('SANDBOX_USERNAME'),
                'PWD'       => env('SANDBOX_PWD'),
                'SIGNATURE' => env('SANDBOX_SIGNATURE')
            )
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (!$response) {
            throw new Exception('Failed to contact PayPal API: ' . curl_error($ch) . ' (Error No. ' . curl_errno($ch) . ')');
        }
        curl_close($ch);
        parse_str($response, $result);
        return response()->json($this->decodeFields($result));
    }

    public function create_payment(Request $request)
    {
        try {
            $session = $request->session();
            $amount = $request['amount'];
            $hash = Str::random(40);
            $data = [
                'intent' => 'sale',
                'payer' => [
                    'payment_method' => 'paypal'
                ],
                'transactions' => [[
                    'amount' => [
                        'total' => $amount,
                        'currency' => 'MXN'
                    ]
                ]],
                "redirect_urls" => [
                    "return_url" => url('aproved').'/'.$hash,
                    "cancel_url" => "https://example.com"
                ]

            ];
            $amount_data = $this->curl_json([
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_URL            => 'https://api.sandbox.paypal.com/v1/payments/payment',
                CURLOPT_HTTPHEADER     => ['Content-Type:application/json', 'Authorization:Bearer ' . $this->tokens['access_token']]
            ]);
            if (!empty($amount_data) && gettype($amount_data) == "array") {
                if (!empty($amount_data['payer'])) {
                    $payment_id = $amount_data['id'];
                    $url = null;
                    for ($i=0; $i < count($amount_data['links']); $i++) {
                        if ($amount_data['links'][$i]['rel'] == 'approval_url') {
                            $url = $amount_data['links'][$i]['href'];
                        }
                    }
                    if (!empty($url)) {
                        $response = [
                            "url"=> $url,
                            "id"=>$payment_id
                        ];
                        $session->put($hash,$response);
                        return response()->json($response);
                    }

                }

            }
            return response()->json([], 400);
        } catch (\Exception $th) {
            \Log::info($th);
        }
    }

    public function aproved_payment ($hash, Request $request) {
        $session = $request->session();
        $payment_data = $session->get($hash);
        $response = false;
        if (!empty($payment_data)){
            $status_payment = $this->get_status_payment($payment_data);
            if (!empty($status_payment)){
                if (!empty($status_payment['payer']) && $status_payment['payer']['status']=='VERIFIED') {
                    $payer_id = $status_payment['payer']['payer_info']['payer_id'];
                    $payment_data['payer_id'] = $payer_id;
                    $executed = $this->pay_execute($payment_data);
                    if (!empty($executed)){
                        $session->forget($hash);
                        $response = true;
                    }
                }
            }
        }
        return view('aproved', ["response" => $response]);
    }

    private function encodeFields(array $fields)
    {
        return array_map('urlencode', $fields);
    }

    private function decodeFields(array $fields)
    {
        return array_map('urldecode', $fields);
    }

}
