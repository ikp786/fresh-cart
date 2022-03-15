@extends('admin.layouts.app')
@section('style')
<style>
    #removePropImgRow {
        padding: 12px;
        margin-left: 3px;
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
                    <span class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Add</span>
                    <h3 class="fw-bold mb-0"></h3>
                    <a href="{{route('admin.products.index')}}" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">lIST</a>
                </div>
            </div>
        </div> <!-- Row end  -->
        <div class="row g-3 mb-3">
            <div class="col-lg-12">
                <div class="card mb-3">
                    <div class="card-header py-3 d-flex justify-content-between bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold ">Products</h6>
                    </div>
                    <div class="card-body">
                        {!! Form::model($products, ['method' => 'PATCH','route' => ['admin.products.update', $products->id],'files'=>true]) !!}
                        @csrf
                        <!-- <div class="row g-3 align-items-center"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                {{ Form::text('name',$products->name,['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                {{ Form::text('price',$products->price,['class' => 'form-control']) }}
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                {{ Form::select('category_id', $categories, $products->category_id, ['class' => 'form-control','id' => 'category_id']) }}
                            </div>
                            <div class="col-md-6">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                                    <h6 class="m-0 fw-bold">Status{{$products->status}} </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" {{ ($products->status=="1") ? "checked" : "" }} type="radio" name="status" value="1"  >
                                        <label class="form-check-label">
                                            Published
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" {{ ($products->status=="0") ? "checked" : "" }} type="radio" value="0" name="status">
                                        <label class="form-check-label">
                                            Unpublish
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                {{ Form::textarea('description',$products->description,['rows' => 4,'id' => 'description', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="">Images</label>
                            @foreach($products->allImages as $key => $val)
                            @php $image = isset($val->image) ? $val->image : ''; @endphp
                            <span id="{{$val->id}}"><img style="height: 50px; width:50px;" src="{{asset('storage/app/public/product_images/'.$image)}}" alt=""> <button onclick="deleteImage({{$val->id}})" type="button">X</button></span>
                            @endforeach
                        </div>
                        <div class="row">
                            <!-- <label for=""> Images</label> -->
                            <div class="col-md-6">
                                <input type="file" name="image[]" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <button id="addPropertyImgeRow" type="button" class="btn btn-primary btn-info">Add Row</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="newPropImgRow"></div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary py-2 px-5 text-uppercase btn-set-task w-sm-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><!-- Row end  -->
</div>
</div>
@endsection
@section('script')
<script>
    function deleteImage(id) {
        $.ajax({
            type: 'DELETE',
            dataType: "json",
            url: '{{route("admin.products.delete-product-image")}}',
            data: {
                'id': id
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.status) {
                    $('#' + id).remove('');
                } else {
                    alert('something wrong');
                }
            }
        });
    }
    $("#addPropertyImgeRow").click(function() {
        var html = '';
        html += '<div id="inputPropImgRow">';
        html += '<div class="input-group mb-3">';
        html += '<input type="file" class="form-control" name="image[]" />';
        html += '<div class="input-group-append">';
        html += '<button id="removePropImgRow" type="button" class="btn btn-danger"><i class="icofont-ui-delete text-danger"></i></button>';
        html += '</div>';
        html += '</div>';

        $('#newPropImgRow').append(html);

    });

    $(document).on('click', '#removePropImgRow', function() {
        $(this).closest('#inputPropImgRow').remove();
    });

    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection