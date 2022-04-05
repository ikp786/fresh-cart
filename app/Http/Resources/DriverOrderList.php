<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverOrderList extends JsonResource
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
        $data =  [
            'id'                     => $this->id,
            'order_id'               => $this->order_number,
            'name'                   => isset($this->addresses->name) ? $this->addresses->name : '',
            'product_name'           => $product_name,
            'order_date'             => $this->created_at,
            'order_delivery_status'  => $this->order_delivery_status,
            'payment_method'         => $this->payment_method,
            'total_amount'           => $this->order_amount
            
        ];

        if($this->order_delivery_status == 'Pending'){
            $data['pending_address'] = isset($this->addresses->name) ? new AddressCollection($this->addresses) : '';
        }else{
            $data['address'] = isset($this->addresses->name) ? new AddressCollection($this->addresses) : '';
        }
        return $data;
    }
}
