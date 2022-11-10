@extends('admin.layouts.app')
@section('style')
<style>
    td {
        text-align: left !important;
    }
</style>
@endsection
@section('content')
@include('admin.inc.validation_message')
@include('admin.inc.auth_message')
<!-- Body: Body -->
<div class="body d-flex py-3">
    <div class="container-xxl">
        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <span class="btn  py-2 px-5 text-uppercase btn-set-task w-sm-100">New Orders</span>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="example" class="table table-hover align-middle mb-0" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Order Id</th>
                                    <th>Order <br> Date</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Order<br> Amount</th>
                                    <th>Payment<br> Method</th>
                                    <th>Product Name</th>
                                    <th>Order <br> Status</th>
                                    <th>Address</th>
                                    <th>Pincode</th>
                                    <th>Asign <br> Driver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $val)
                                <tr>
                                    <td><strong> <a href="{{route('admin.orders.order-product',$val->id)}}"> {{$val->order_number}}</a></strong></td>
                                    <td> {{$val->created_at}} </td>
                                    <td>{{$val->users->name}}</td>
                                    <td>{{$val->users->email}}</td>
                                    <td>{{$val->users->mobile}}</td>
                                    <td>{{$val->order_amount}}</td>
                                    <td>{{$val->payment_method}}</td>
                                    <td>
                                        @if(is_array($val->orderProductList) || is_object($val->orderProductList))
                                        @forelse($val->orderProductList as $products)
                                        <span><b> Name:</b> {{ $products->product_name ?? '' }}</span><br>
                                        @isset($products->product_quantity_phav)
                                        <span><b> 250 Gram:</b> {{ $products->product_quantity_phav ?? '' }}</span><br>
                                        @endisset
                                        @isset($products->product_quantity_half_kg)
                                        <span><b> 500 Gram:</b> {{ $products->product_quantity_half_kg ?? '' }}</span><br>
                                        @endisset
                                        @isset($products->product_quantity_kg)
                                        <span><b> 1000 Gram:</b> {{ $products->product_quantity_kg ?? '' }}</span><br>
                                        @endisset
                                        @isset($products->offer_product_qty)
                                        <span><b> Offer :</b>
                                            @if($products->offer_product_qty == 'kg')
                                            1000 Gram
                                            @elseif($products->offer_product_qty == 'half_kg')
                                            500 Gram
                                            @else
                                            250 Fram
                                            @endif
                                            {{ $products->offer_product_name ?? '' }}</span><br>
                                        @endisset
                                        @empty
                                        @endforelse
                                        @endif
                                    </td>
                                    <td>{{$val->order_delivery_status}}</td>
                                    <td>{{isset($val->addresses->address) ? $val->addresses->address : ''}}</td>
                                    <td>{{isset($val->addresses->pincode) ? $val->addresses->pincode : ''}}</td>
                                    <!-- <td>{{-- Form::select('driver_id', $drivers, $val->driver_id,['id'=>$val->id,'order_id'=>$val->id, 'class'=>'asign-driver', 'placeholder' =>'asign driver']) --}}</td> -->
                                    <td>
                                        <select name="driver_id" id="{{$val->id}}" order_id="{{$val->id}}" class="asign-driver">
                                            <option value="">Asign Driver</option>
                                            @foreach($drivers as $driver)
                                            <option value="{{$driver->id}}">{{$driver->name.' '.$driver->mobile}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- Row end  -->
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csvHtml5'
            ]
        });
        $('.asign-driver').change(function() {

            if (!confirm("Do you want Asign  driver")) {
                return false;
            }
            var driver_id = $(this).val();
            var order_id = $(this).attr('order_id');
            if (driver_id != '') {
                $.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: "{{ route('admin.orders.asign-driver') }}",
                    data: {
                        'order_id': order_id,
                        'driver_id': driver_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        if (result.status) {
                            swal("Success!", "Driver asign", "success");
                        } else {
                            swal("Soory!", "this driver is Offline", "error");
                        }
                    }
                });
            }
        });
    });
</script>
@endsection