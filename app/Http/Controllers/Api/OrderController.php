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

class OrderController extends BaseController
{
    public function createOrder(StoreOrderRequest $request)
    {
        try {
            \DB::beginTransaction();
            $checkCarts = Cart::where('user_id', auth()->user()->id)->get()->toArray();            
            if(empty($checkCarts)){
                return $this->sendFailed('SORRY! NO PRODUCT FOUND IN CART', 200);
            }
            // GET DELIVERY CHARGE
            $delivery_charge                = Setting::value('deliver_charge');
            // GET OFFER DETAIL 
            $offer_list                     = Offer::where('status', 1)->with('products')->OrderBy('id', 'desc')->limit(1)->get();
            $offer_product_name             = '';
            $offer_product_qty              = '';
            if (!empty($offer_list)) {
                $minimum_order_value            = $offer_list[0]->minimum_order_value;
                // CHECK OFFER FOR THIS ORDER APLICABLE OR NOT
                if ($request->order_amount >= $minimum_order_value) {
                    $offer_product_name         = $offer_list[0]->products->name;
                    $offer_product_qty          = $offer_list[0]->quantity_type;
                }
            }
            // SAVE ORDER ID IN ORDER TABLE
            $orders                         = new Order();
            $orders->order_number           = $request->order_id;
            $orders->offer_product_qty      = $offer_product_qty;
            $orders->offer_product_name     = $offer_product_name;
            $orders->order_amount           = $request->order_amount;
            $orders->deliver_charge         = $delivery_charge;
            $orders->mobile                 = auth()->user()->mobile;
            $orders->email                  = auth()->user()->email;
            $orders->payment_method         = $request->payment_method;
            $orders->payment_status         = $request->payment_method == 'Online' ? 'Success' : 'Pending';
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
                $value->delete();
            }
            // SAVE ORDER PLACE ADDRESS DETAILS
            $address                        = new Address();
            $address->order_id              = $orders->id;
            $address->name                  = $request->name;
            $address->mobile                = $request->mobile;
            $address->email                 = $request->email;
            $address->address               = $request->address;
            $address->pincode               = $request->pincode;
            $address->type                  = $request->address_type;
            auth()->user()->address()->save($address);
            \DB::commit();
            return $this->sendSuccess('ORDER CREATE SUCCESSFULL');
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
            $orders = auth()->user()->orders()->where('order_delivery_status', $status)->with('addresses')->get();

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
            $orders = auth()->user()->orders()->where('id', $order_id)->with('addresses')->first();

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
            $orders = auth()->user()->driverOrders()->where('driver_id', auth()->user()->id)->where('order_delivery_status', 'Pending')->with('addresses')->get();
            $total_order = auth()->user()->driverOrders()->where('driver_id', auth()->user()->id)->count();
            $total_amount = auth()->user()->driverOrders()->where('driver_id', auth()->user()->id)->sum('deliver_charge');

            $profile_pic   = !empty(auth()->user()->profile_pic) ? asset('storage/app/public/user_images/' . auth()->user()->profile_pic) : asset('storage/user_images/logo.png');
            $profile      = [
                'name' => auth()->user()->name,
                'online_status' => auth()->user()->online_status,
                'profile_pic' => $profile_pic
            ];
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', ['order_list' => DriverOrderList::collection($orders), 'total_income' => $total_amount, 'total_order' => $total_order,'driver_info' => $profile]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getDriverOrderList()
    {
        try {
        //    echo auth()->user()->id;die;
            $pending_orders = auth()->user()->driverOrders()->where('order_delivery_status', 'Pending')            
            ->with('addresses')->get();
            $deliver_orders = auth()->user()->driverOrders()->where('order_delivery_status', 'Deliver')            
            ->with('addresses')->get();
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
            $orders = auth()->user()->driverOrders()->where('id', $order_id)->with('addresses')->first();
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
            'order_id.exist'               => 'wrong order id',
            'driver_payment_type.required' => 'Driver Payment type required if payment mehotd Cod'
        ];
        $rules = [
            'order_id'                  => 'required|exists:orders,id',
        ];
        $orders = Order::where(['id' => $request->order_id, 'driver_id' => auth()->user()->id])->first();
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
        $orders = auth()->user()->orders()->save($orders);
        return $this->sendSuccess('ORDER DELIVER SUCCESSFULLY');
    }
}
