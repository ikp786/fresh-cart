<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product_name = OrderProduct::where('order_id',$this->id)->pluck('product_name')->join(',');
        return
            [ 
                'id'                     => $this->id,
                'order_id'               => $this->order_number,
                'name'                   => $this->addresses->name,
                'product_name'           => $product_name,
                'order_date'             => date('d/m/Y',strtotime($this->created_at)),
                'order_delivery_status'  => $this->order_delivery_status,
                'total_amount'           => $this->order_amount,
                'payment_method'         => $this->payment_method,
                'address'                => new AddressCollection($this->addresses),
            ];
    }
}
