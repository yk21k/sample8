<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Mail\OrderPaid;
use Illuminate\Mail\Mailable;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Support\Facades\Mail;

class PayPalController extends Controller
{
    public function getExpressCheckout($orderId)
    {
        $checkoutData = $this->checkoutData($orderId);

        $provider = new ExpressCheckout();

        $response = $provider->setExpressCheckout($checkoutData);

        return redirect($response['paypal_link']);
    }

    private function checkoutData($orderId)
    {
        $cart = \Cart::session(auth()->id());

        // dd($cart->getContent()->toArray());


        $cartItems = array_map(function($item){
            return [
                'name' => $item['name'],
                'price' => $item['price'],
                'qty' => $item['quantity']
            ];
        }, $cart->getContent()->toArray());

        // dd($cartItems);

        $checkoutData = [
            'items' => $cartItems,

            'return_url' => route('paypal.success', $orderId),
            'cancel_url' => route('paypal.cancel'),
            'invoice_id' => uniqid(),
            'invoice_description' => " Order description ",
            'total' => $cart->getTotal()
        ];

        return $checkoutData;
    }

    public function cancelPage()
    {
        dd('payment failed');
    }

    public function getExpressCheckoutSuccess(Request $request, $orderId)
    {
        $token = $request->get('token');

        $payerId = $request->get('PayerID');

        $provider = new ExpressCheckout();

        $checkoutData = $this->checkoutData($orderId);


        $response = $provider->getExpressCheckoutDetails($token);


        if(in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])){
            
            // Perform transaction Paypal
            $payment_status = $provider->doExpressCheckoutPayment($checkoutData, $token, $payerId);
            $status = $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'];

            if(in_array($status, ['Completed', 'Processed'])){
                $order = Order::find($orderId);
                $order->is_paid = 1;
                $order->save();
                \Cart::session(auth()->id())->clear();
                // send mail
                Mail::to($order->user->email)->send(new OrderPaid($order));

                return redirect()->route('home')->withMessage('Payment Success!');
            }  

        }
        return redirect()->route('home')->withError('Payment UnSuccessful Something went wrong!');


        // dd('Payment Successful');
    }
}
