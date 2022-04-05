<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products   = Product::with('images', 'categories')->get();

        $title      = 'products';
        $data       = compact('title', 'products');
        return view('admin.products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title        = 'products';
        $categories   = Category::pluck('name', 'id');
        $data         = compact('title', 'categories');
        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $products      = new Product();
            $products->fill($request->all());
            if ($request->freshfromthefarm == 1) {
                $request->freshfromthefarm  = 1;
            } else {
                $products->freshfromthefarm  = 0;
            }
            $products->save();

            if ($request->has('image')) {
                foreach ($request->file('image') as $images_data) {
                    $image_name = time() . '_' . rand(1111, 9999) . '_' . $products->id . '.' . $images_data->getClientOriginalExtension();
                    $images_data->storeAs('product_images', $image_name, 'public');
                    $product_image = new ProductImage();
                    $product_image->product_id    = $products->id;
                    $product_image->image        = $image_name;
                    $product_image->save();
                }
            }
            return redirect()->route('admin.products.index')->withSuccess('Product added success.');
        } catch (\Throwable $e) {
            \DB::rollback();
            return redirect()->back()->with('Failed', $e->getMessage() . ' on line ' . $e->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, $id)
    {
        $title        = 'products';
        $categories   = Category::pluck('name', 'id');
        $products     = Product::with('allImages')->find($id);

        $data         = compact('title', 'categories', 'products');
        return view('admin.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product, $id)
    {
        try {
            $products      = Product::find($id);
            $products->fill($request->all());
            if ($request->freshfromthefarm == 1) {
                $request->freshfromthefarm  = 1;
            } else {
                $products->freshfromthefarm  = 0;
            }
            $products->save();
            if ($request->has('image')) {
                foreach ($request->file('image') as $images_data) {
                    $image_name = time() . '_' . rand(1111, 9999) . '_' . $products->id . '.' . $images_data->getClientOriginalExtension();
                    $images_data->storeAs('product_images', $image_name, 'public');
                    $product_image = new ProductImage();
                    $product_image->product_id    = $products->id;
                    $product_image->image        = $image_name;
                    $product_image->save();
                }
            }
            return redirect()->route('admin.products.index')->withSuccess('Product update success.');
        } catch (\Throwable $e) {
            \DB::rollback();
            return redirect()->back()->with('Failed', $e->getMessage() . ' on line ' . $e->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function deleteProductImage(Request $request)
    {
        if (request()->ajax()) {
            $doc = ProductImage::findOrfail($request->id);
            if (!empty($doc)) {
                if (Storage::disk('public')->exists('product_images/' . $doc->image)) {
                    Storage::disk('public')->delete('product_images/' . $doc->image);
                }
                $doc->delete();
                return response()->json(['status' => true]);
            }
            return response()->json(['status' => false]);
        }
    }
}
