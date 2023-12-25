<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
// stripe classes import
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Checkout\Session;
use Stripe\Climate\Order as StripeOrder;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Exception\SignatureVerificationException;



class PaymentController extends Controller
{
    //
    public function checkout(Request $request)
    {
        try {
            $user = $request->user();
            $token = $user->token;
            $courseId = $request->courseId;

            /*
                Stripe API Key
            */
            Stripe::setApiKey('sk_test_51OQvRQFStIRNc2J3GmlfrJPGNQX0WFl6JrDtEqKx9S2F5AH8RXjF5gjGshqyeF28YeC2fIpYLRqVxyYIfuvaM70x00g1JJQRjT');

            $courseResult = Course::where('id', '=', $courseId)->first();
            if (empty($courseResult)) {

                return response()->json([
                    "status" => false,
                    "message" => "Course $courseId not found",
                    "data" => $courseResult,
                ], 400);
            }

            $orderMap = [];
            $orderMap['course_id'] = $courseId;
            $orderMap['user_token'] = $token;
            $orderMap['status'] = 1;

            $orderRes = Order::where($orderMap)->first();
            // if order has been placed or not
            if (!empty($orderRes)) {
                return response()->json([
                    "status" => false,
                    "message" => "Order has been placed",
                    "data" => $orderRes,
                ], 400);
            }

            $orderMap['total_amount'] = $courseResult->price;
            $orderMap['status'] = 0;
            $orderMap['created_at'] = Carbon::now();

            // New Order for user
            $MY_DOMAIN = env('APP_URL');
            $orderId = Order::insertGetId($orderMap);

            // create payment session
            $checkoutSession = Session::create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $courseResult->name,
                                'description' => $courseResult->description,
                                // 'images' => [$MY_DOMAIN . '/' . $courseResult->thumbnail],

                            ],
                            'unit_amount' => intval($courseResult->price * 100),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => $orderId,
                        'user_token' => $token,
                    ],
                ],
                'metadata' => [
                    'order_id' => $orderId,
                    'user_token' => $token,
                ],
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'success_url' => $MY_DOMAIN . 'success',
                'cancel_url' => $MY_DOMAIN . 'cancel',
            ]);



            return response()->json([
                "status" => true,
                "message" => "Payment Session Created",
                "data" => $checkoutSession->url,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
                "data" => $th->getTrace(),
            ], 500);
        }
    }
}
