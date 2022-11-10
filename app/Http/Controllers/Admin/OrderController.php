<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function newOrderList()
    {
        $orders     =  Order::with('users', 'addresses', 'orderProductList')->whereNull('driver_id')->get();
        // dd($orders);
        $drivers    =  User::where('role', 3)->get(['name', 'id','mobile']);
        // dd($drivers);
        $title      =  'Order';
        $data       =  compact('title', 'orders', 'drivers');
        return view('admin.orders.order-new', $data);
    }

    public function oldOrderList()
    {
        $orders     =  Order::with('users', 'addresses', 'drivers','orderProductList')->whereNotNull('driver_id')->get();
        $title      =  'Order';
        $data       =  compact('title', 'orders');
        return view('admin.orders.order-old', $data);
    }

    public function orderProduct($id)
    {

        $orders     =  OrderProduct::where('order_id', $id)->with('orders', 'orders.addresses')->get();
        $title      =  'Orders';
        $data       =  compact('title', 'orders');
        return view('admin.orders.order-product', $data);
    }

    public function asignDriver(Request $request)
    {
        if (request()->ajax()) {
            $driver = User::find($request->driver_id);
            if($driver->online_status == "Online"){
            $order = Order::find($request->order_id);
            $order->driver_id = $request->driver_id;
            $order->save();
            return response()->json(['success' => 'driver asign successfully.','status'=>true]);
            }else{
                return response()->json(['error' => 'Sorry! this driver is  offline.','status'=>false]);
            }
        }
    }
}
