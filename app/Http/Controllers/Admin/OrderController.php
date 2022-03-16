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
        $orders     =  Order::with('users', 'addresses')->whereNull('driver_id')->get();        
        $drivers    =  User::where('role', 3)->pluck('name','id');        
        $title      =  'Order';
        $data       =  compact('title', 'orders', 'drivers');
        return view('admin.orders.order-new', $data);
    }

    public function oldOrderList()
    {
        $orders     =  Order::with('users', 'addresses','drivers')->whereNotNull('driver_id')->get();                
        $title      =  'Order';
        $data       =  compact('title', 'orders');
        return view('admin.orders.order-old', $data);
    }

    public function orderProduct($id)
    {
        
        $orders     =  OrderProduct::where('order_id',$id)->with('orders','orders.addresses')->get();        
        $title      =  'Orders';
        $data       =  compact('title', 'orders');
        return view('admin.orders.order-product', $data);
    }

    public function asignDriver(Request $request)
    {
        if(request()->ajax()){        
            $order = Order::find($request->order_id);
            $order->driver_id = $request->driver_id;
            $order->save();
            return response()->json(['success' => 'driver asign successfully.']);
        }
    }
}
