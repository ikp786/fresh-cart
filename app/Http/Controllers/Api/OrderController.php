<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $orders                         = new Order();
        $orders->order_number           = $request->order_id;
        $orders->order_amount           = $request->order_amount;
        $orders->mobile                 = auth()->user()->mobile;
        $orders->email                  = auth()->user()->email;
        $orders->payment_method         = $request->payment_method;
        $orders->payment_status         = $request->payment_method == 'Online' ? 'Success' : 'Pending';
        $orders->save();

        $cats = Cart::where('user_id', auth()->user()->id)->get();
        foreach ($cats as $key => $value) {
            $products                                   = Product::find($value->product_id);
            $order_product                              = new OrderProduct();
            $order_product->product_name                = $products->name;
            $order_product->product_description         = $products->description;
            $order_product->product_quantity_phav       = $value->product_quantity_phav;
            $order_product->product_quantity_half_kg    = $value->product_quantity_half_kg;
            $order_product->product_quantity_kg         = $value->product_quantity_kg;
            $order_product->product_total_quantity      = $products->product_total_quantity;
            $order_product->product_phav_amount         = $products->pav_price * $value->product_quantity_phav;
            $order_product->product_half_kg_amount      = $products->half_kg_price * $value->product_quantity_half_kg;
            $order_product->product_kg_amount           = $products->kg_price * $value->product_quantity_kg;
            $order_product->total_amount                = $products->name;
            $order_product->save();
        }

        $address                        = new Address();
        $address->order_id              = $orders->id;
        $address->name                  = $request->name;
        $address->mobile                = $request->mobile;
        $address->email                 = $request->email;
        $address->address               = $request->address;
        $address->pincode               = $request->pincode;
        $address->save();
    }
}
