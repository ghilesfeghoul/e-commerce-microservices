<?php

namespace App\Http\Controllers;

use App\Events\OrderCompletedEvent;
use App\Http\Resources\OrderResource;
use App\Jobs\OrderCompleted;
use App\Models\Link;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::with('orderItems')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'zip' => 'required|string',
            'products' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$link = Link::where('code', $request->input('code'))->first()) {
            return response([
                'error' => 'Invalid code'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            \DB::beginTransaction();

            $order = new Order();

            $order->code = $link->code;
            $order->user_id = $link->user->id;
            $order->ambassador_email = $link->user->email;
            $order->first_name = $request->input('first_name');
            $order->last_name = $request->input('last_name');
            $order->email = $request->input('email');
            $order->address = $request->input('address');
            $order->country = $request->input('country');
            $order->city = $request->input('city');
            $order->zip = $request->input('zip');

            $order->save();

            $lineItems = [];

            foreach ($request->input('products') as $item) {
                $product = Product::find($item['product_id']);

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_title = $product->title;
                $orderItem->price = $product->price;
                $orderItem->quantity = $item['quantity'];
                $orderItem->ambassador_revenue = 0.1 * $product->price * $item['quantity'];
                $orderItem->admin_revenue = 0.9 * $product->price * $item['quantity'];

                $orderItem->save();

                $lineItems[] = [
                    'name' => $product->title,
                    'description' => $product->description,
                    'images' => [
                        $product->image
                    ],
                    'amount' => 100 * $product->price,
                    'currency' => 'usd',
                    'quantity' => $item['quantity']
                ];
            }

            $stripe = Stripe::make(env('STRIPE_SECRET'));

            $source = $stripe->checkout()->sessions()->create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'success_url' => env('CHECKOUT_URL') . '/success?source={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('CHECKOUT_URL') . '/error'
            ]);

            $order->transaction_id = $source['id'];
            $order->save();

            \DB::commit();

            return $source;
        } catch (\Throwable $e) {
            \DB::rollBack();

            return response([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function confirm(Request $request)
    {
        if (!$order = Order::where('transaction_id', $request->input('source'))->first()) {
            return response([
                'error' => 'Order not found!'
            ], 404);
        }

        $order->complete = 1;
        $order->save();

        $array = $order->toArray();
        $array['ambassador_revenue'] = $order->ambassador_revenue;
        $array['admin_revenue'] = $order->admin_revenue;
        $array['order_items'] = $order->orderItems->toArray();

        OrderCompleted::dispatch($array)->onQueue('email_queue');

        return [
            'message' => 'success'
        ];
    }
}
