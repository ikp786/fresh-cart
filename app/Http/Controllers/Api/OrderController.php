<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\DriverOrderDetail;
use App\Http\Resources\DriverOrderList;
use App\Http\Resources\UserOrderList;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Ratting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Validator;
use Razorpay\Api\Api;

class OrderController extends BaseController
{
    function savePaymentResponse(Request $request)
    {
        try {
            $error_message = [
                'user_type.required'           => 'User type should be required',
            ];
            $rules = [
                'order_id'                   => 'required|exists:orders,id',
                'payment_status'             => 'required|In:Success,Failed',
            ];

            $validator = Validator::make($request->all(), $rules, $error_message);
            if ($validator->fails()) {
                return $this->sendFailed($validator->errors()->first(), 200);
            }
            if ($request->payment_status == 'Success') {
                \DB::beginTransaction();
                $orders  = auth()->user()->orders()->find($request->order_id);
                if (!isset($orders) || $orders == null) {
                    return $this->sendFailed('SORRY! WRONG ORDER ID', 200);
                }
                $orders->payment_status = $request->payment_status;
                $orders->is_order = 1;
                $orders->save();
                auth()->user()->carts()->delete();
                \DB::commit();
                return $this->sendSuccess('ORDER SAVE SUCCESSFULL');
            } else {
                \DB::beginTransaction();
                $orders  = auth()->user()->orders()->find($request->order_id);
                if (!isset($orders) || $orders == null) {
                    return $this->sendFailed('SORRY! WRONG ORDER ID', 200);
                }
                $orders->payment_status = $request->payment_status;
                $orders->save();
                \DB::commit();
                return $this->sendFailed('YOUR ORDER PAYMENT IS FAILED. PLEASE TRY AGAIN', 200);
            }
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function createOrder(StoreOrderRequest $request)
    {
        try {
            $delivery_charge                = Setting::value('deliver_charge');
            $checkCarts = Cart::where('user_id', auth()->user()->id)->get()->toArray();
            if (empty($checkCarts)) {
                return $this->sendFailed('SORRY! NO PRODUCT FOUND IN CART', 200);
            }
            $total_amount = 0;
            foreach ($checkCarts as $key => $val) {
                $productsData           = Product::where('id', $val['product_id'])->first();
                $pav_price              = (int)$productsData->pav_price      * (int)$val['product_quantity_phav'];
                $half_kg_price          = (int)$productsData->half_kg_price  * (int)$val['product_quantity_half_kg'];
                $kg_price               = (int)$productsData->kg_price       * (int)$val['product_quantity_kg'];
                $single_product_price   = $pav_price + $half_kg_price + $kg_price;
                $total_amount           = $total_amount + $single_product_price;
            }
            $total_amount = $total_amount + $delivery_charge;
            if ($request->payment_method == 'Online') {
                $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRATE'));
                $orderData = [
                    'receipt'         => 'rcptid_11',
                    'amount'          => $total_amount * 100, // 39900 rupees in paise
                    'currency'        => 'INR'
                ];
                $razorpayOrder = $api->order->create($orderData);
            }
            \DB::beginTransaction();
            // GET DELIVERY CHARGE

            // GET OFFER DETAIL 
            $offer_list                     = Offer::where('status', 1)->with('products')->first();
            $offer_product_name             = '';
            $offer_product_qty              = '';
            if (!empty($offer_list)) {
                $minimum_order_value            = $offer_list->minimum_order_value;
                // CHECK OFFER FOR THIS ORDER APLICABLE OR NOT
                if ($total_amount >= $minimum_order_value) {
                    $offer_product_name         = $offer_list->products->name;
                    $offer_product_qty          = $offer_list->quantity_type;
                }
            }
            $order_id = date('Ymd') . rand(1000, 9999) . auth()->user()->id;
            // SAVE ORDER ID IN ORDER TABLE

            $orders                         = new Order();
            $orders->order_number           = $order_id;
            if ($request->payment_method == 'Online') {
                $orders->razorpay_id        = $razorpayOrder->id;
            } else {
                $orders->is_order = 1;
            }
            $orders->address_id             = $request->address_id;
            $orders->offer_product_qty      = $offer_product_qty;
            $orders->offer_product_name     = $offer_product_name;
            $orders->order_amount           = $total_amount;
            $orders->deliver_charge         = $delivery_charge;
            $orders->mobile                 = auth()->user()->mobile;
            $orders->email                  = auth()->user()->email;
            $orders->payment_method         = $request->payment_method;
            $orders->payment_status         = 'Pending';
            $orders = auth()->user()->orders()->save($orders);
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            foreach ($carts as $key => $value) {
                // GET PRODCUT DETAIL PRODUCT TABLE
                $products                                   = Product::find($value->product_id);
                // TOTAL AMOUNT SUM 
                $total_amopunt                              = $products->pav_price * $value->product_quantity_phav + $products->half_kg_price * $value->product_quantity_half_kg + $products->kg_price * $value->product_quantity_kg;
                // SAVE ORDER PRODUCT DETAIL
                $order_product                              = new OrderProduct();
                $order_product->order_id                    = $orders->id;
                $order_product->product_id                  = $products->id;
                $order_product->product_name                = $products->name;
                $order_product->product_description         = $products->description;
                $order_product->product_quantity_phav       = $value->product_quantity_phav;
                $order_product->product_quantity_half_kg    = $value->product_quantity_half_kg;
                $order_product->product_quantity_kg         = $value->product_quantity_kg;
                $order_product->product_total_quantity      = $value->product_total_quantity;
                $order_product->product_phav_amount         = $products->pav_price * $value->product_quantity_phav;
                $order_product->product_half_kg_amount      = $products->half_kg_price * $value->product_quantity_half_kg;
                $order_product->product_kg_amount           = $products->kg_price * $value->product_quantity_kg;
                $order_product->total_amount                = $total_amopunt;
                $order_product->save();
                if ($request->payment_method == 'Cod') {
                    $value->delete();
                }
            }
            // SAVE ORDER PLACE ADDRESS DETAILS
            // $address                        = new Address();
            // $address->order_id              = $orders->id;
            // $address->name                  = $request->name;
            // $address->mobile                = $request->mobile;
            // $address->email                 = $request->email;
            // $address->address               = $request->address;
            // $address->pincode               = $request->pincode;
            // $address->type                  = $request->address_type;
            // auth()->user()->address()->save($address);
            \DB::commit();
            if ($request->payment_method == 'Cod') {
                return $this->sendSuccess('ORDER CREATE SUCCESSFULL', ['razorpay_id' => "", 'order_id' => $orders->id]);
            }
            return $this->sendSuccess('ORDER CREATE SUCCESSFULL', ['razorpay_id' => $razorpayOrder->id, 'order_id' => $orders->id]);
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getUserOrderList($status)
    {
        try {
            if ($status == 'Pending' || $status == 'Deliver') {
            } else {
                return $this->sendFailed('Sorry! Status accept only Pending or Deliver', 400);
            }
            $orders = auth()->user()->orders()->where('is_order', 1)->where('order_delivery_status', $status)->with('addresses')->orderBy('id','desc')->get();
            // dd($orders);
            if (!isset($orders) || count($orders) == 0) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }

            return $this->sendSuccess('ORDER GET SUCCESSFULLY', UserOrderList::collection($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getUserOrderDetail($order_id)
    {
        try {
            $orders = auth()->user()->orders()->where('is_order', 1)->where('id', $order_id)->with('addresses')->first();

            if (!isset($orders)) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', new UserOrderList($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function driverDashboard()
    {
        try {
            $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('driver_id', auth()->user()->id)->where('order_delivery_status', 'Pending')->with('addresses')->orderBy('id','desc')->get();
            $total_order = auth()->user()->driverOrders()->where('is_order', 1)->where('driver_id', auth()->user()->id)->count();
            $total_amount = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', 'Deliver')->where('driver_id', auth()->user()->id)->sum('deliver_charge');

            $profile_pic   = !empty(auth()->user()->profile_pic) ? asset('storage/app/public/user_images/' . auth()->user()->profile_pic) : asset('storage/user_images/logo.png');
            $profile      = [
                'name' => auth()->user()->name,
                'online_status' => auth()->user()->online_status,
                'profile_pic' => $profile_pic
            ];
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', ['order_list' => DriverOrderList::collection($orders), 'total_income' => $total_amount, 'total_order' => $total_order, 'driver_info' => $profile]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getDriverOrderList()
    {
        try {
            //    echo auth()->user()->id;die;
            $pending_orders = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', 'Pending')
                ->with('addresses')->orderBy('id','desc')->get();
            $deliver_orders = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', 'Deliver')
                ->with('addresses')->orderBy('id','desc')->get();
            $data  = [
                'pending'  => DriverOrderList::collection($pending_orders),
                'deliver'  => DriverOrderList::collection($deliver_orders)
            ];

            return $this->sendSuccess('ORDER GET SUCCESSFULLY', $data);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getDriverOrderDetail($order_id)
    {
        try {
            $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('id', $order_id)->with('addresses')->first();
            if (!isset($orders)) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', new DriverOrderDetail($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // SENT FEEDBACK
    public function createFeedback(Request $request)
    {
        $error_message =     [
            'order_id.required'            => 'Order ID should be required',
            'ratting_star.required'        => 'Ratting should be required',
            'ratting_comment.required'     => 'Feedback comment should be required',
            'order_id.unique'              => 'You have already submited Feedback this order'
        ];
        $rules = [
            'order_id'                  => 'required|exists:orders,id|unique:rattings,order_id',
            'ratting_star'              => 'required',
            'ratting_comment'           => 'required',
        ];
        $validator = Validator::make($request->all(), $rules, $error_message);
        if ($validator->fails()) {
            return $this->sendFailed($validator->errors()->first(), 200);
        }
        try {
            \DB::beginTransaction();
            $ratting = new Ratting();
            $ratting->fill($request->all());
            $ratting = auth()->user()->ratting()->save($ratting);
            \DB::commit();
            return $this->sendSuccess('FEEDBACK SENT SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function orderDeliverByDriver(Request $request)
    {
        $error_message =     [
            'order_id.required'            => 'Order ID should be required',
            'order_id.exists'               => 'wrong order id',
            'driver_payment_type.required' => 'Driver Payment type required if payment mehotd Cod'
        ];
        $rules = [
            'order_id'                  => 'required|exists:orders,id',
        ];
        $orders = Order::where(['id' => $request->order_id, 'driver_id' => auth()->user()->id])->where('is_order', 1)->first();
        if (!isset($orders)) {
            return $this->sendFailed('UNAUTHORRIZED ACCESS', 200);
        }
        if ($orders->payment_method == 'Cod') {
            $rules['driver_payment_type']  = 'required';
        }
        $validator = Validator::make($request->all(), $rules, $error_message);
        if ($validator->fails()) {
            return $this->sendFailed($validator->errors()->first(), 200);
        }
        $orders = Order::find($request->order_id);
        if ($orders->payment_method == 'Cod') {
            $orders->driver_payment_type = $request->driver_payment_type;
        }
        $orders->order_delivery_status  = 'Deliver';
        $orders->payment_status      = 'Success';
        $orders->save();
        return $this->sendSuccess('ORDER DELIVER SUCCESSFULLY');
    }
}
