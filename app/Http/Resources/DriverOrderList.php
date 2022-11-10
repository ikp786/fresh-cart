<?php

namespace App\Http\Resources;

use App\Models\Address;
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
        $address = Address::find($this->address_id);
        $product_name = OrderProduct::where('order_id',$this->id)->pluck('product_name')->join(',');
        $data =  [
            'id'                     => $this->id,
            'order_id'               => $this->order_number,
            'name'                   => isset($address->name) ? $address->name : '',
            'product_name'           => $product_name,
            'order_date'             => date('d/m/Y',strtotime($this->created_at)),
            'order_delivery_status'  => $this->order_delivery_status,
            'payment_method'         => $this->payment_method,
            'total_amount'           => $this->order_amount            
        ];
        
        if($this->order_delivery_status == 'Pending'){
            $data['pending_address'] = isset($address) ? new AddressCollection($address) : '';
        }else{
            $data['address'] = isset($address) ? new AddressCollection($address) : '';
        }
        return $data;
    }
}
