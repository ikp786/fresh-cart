<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DriverOrderDetail;
use App\Http\Resources\DriverOrderList;
use App\Http\Resources\UserOrderList;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function createOrder(Request $request)
    {
        try {
            \DB::beginTransaction();
            // SAVE ORDER ID IN ORDER TABLE
            $orders                         = new Order();
            $orders->order_number           = $request->order_id;
            $orders->order_amount           = $request->order_amount;
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


    public function getDriverOrderList($payment_method)
    {
        try {
            if ($payment_method == 'Online' || $payment_method == 'Cod') {
            } else {
                return $this->sendFailed('Sorry! Status accept only Online or Cod', 400);
            }
            $orders = auth()->user()->driverOrders()->where('payment_method', $payment_method)->with('addresses')->get();

            if (!isset($orders) || count($orders) == 0) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', DriverOrderList::collection($orders));
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
}
