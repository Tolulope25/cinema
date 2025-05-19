<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{

    public function pay($orderId){

        // $orderId = $request->query('orderId');
        $order = Order::find($orderId);


        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }
     return view('pay.form', compact('order'));

    }

    public function make_payment(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'email' => 'required|email',
            'amount' => 'required|numeric',
        ]);

        $order = Order::findOrFail($validatedData['order_id']);

        $formData = [
            'email' => $validatedData['email'],
            'amount' => $validatedData['amount'] * 100, // Amount in kobo
            'callback_url' => route('pay.callback')
        ];

        $pay = json_decode($this->initiate_payment($formData));

        if ($pay && $pay->status) {
            // Save the reference to the order
            $order->reference = $pay->data->reference;
            $order->save();

            \Log::info('Payment Initiated', [
                'order_id' => $order->id,
                'reference' => $pay->data->reference,
            ]);

            return redirect($pay->data->authorization_url);
        } else {
            $errorMessage = $pay->message ?? 'Something went wrong';
            return back()->with('error', $errorMessage);
        }
    }


    public function payment_callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            \Log::error('Payment callback: Reference not found.');
            return view('pay.order_failed')->with('error', 'Reference not found.');
        }

        $verificationResponse = $this->verify_payment($reference);

        \Log::info('Raw Verify Payment Response:', ['response' => $verificationResponse]);

        // Decode JSON response
        $response = json_decode($verificationResponse);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('JSON decode error:', ['error' => json_last_error_msg()]);
            return view('pay.order_failed')->with('error', 'Unable to process payment verification. Please contact support.');
        }

        \Log::info('Decoded Verify Payment Response:', ['response' => $response]);

        if (isset($response->status) && $response->status) {
            $paymentData = $response->data;

            $order = Order::where('reference', $paymentData->reference)->first();

            if ($order) {
                $order->status = 'paid';
                $order->total_amount = $paymentData->amount / 100;
                $order->save();
  if (Auth::check()) {
                $cart = Auth::user()->cart;
                if ($cart) {
                    $cart->cartItems()->delete();
                }
            } else {
                session()->forget('guest_cart');
            }

                \Log::info('Payment successful', [
                    'order_id' => $order->id,
                    'reference' => $paymentData->reference,
                    'amount' => $order->total_amount,
                ]);

                $data = (object) [
                    'status' => 'success',
                    'order_id' => $order->id,
                    'reference' => $paymentData->reference,
                    'amount_paid' => $order->total_amount,
                ];

                return view('pay.callback_page', compact('data', 'order'));
            } else {
                \Log::error('Payment callback: Order not found for reference: ' . $paymentData->reference);
                return view('pay.order_failed')->with('error', 'Order not found. Please contact support.');
            }
        } else {
            $errorMessage = isset($response->message) ? $response->message : 'Payment verification failed.';
            \Log::error('Payment verification failed:', ['response' => $response]);
            return view('pay.order_failed')->with('error', $errorMessage);
        }
    }

    public function initiate_payment($formData)
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields_string = http_build_query($formData);
        $ch = curl_init();
        $secretKey = env('PAYSTACK_SECRET_KEY');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . trim($secretKey),
            "Cache-Control: no-cache",
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            \Log::error('Curl error during payment initialization: ' . curl_error($ch));
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        \Log::info('Payment Initialization Response', [
            'result' => $result,
            'http_code' => $httpCode,
        ]);

        return $result;
    }


    public function verify_payment($reference)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Log::error('cURL Error:', ['error' => $err]);
            return json_encode(['status' => false, 'message' => 'cURL Error: ' . $err]);
        }

        return $response;
    }

}

