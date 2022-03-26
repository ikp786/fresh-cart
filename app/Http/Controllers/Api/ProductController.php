<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    // PRODUCT CATEGORY LIST
    public function getCategoryList()
    {
        try {
            $cagetory_list = Category::OrderBy('name', 'asc')->get(['id', 'name']);
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('CATEGORY NOT FOUND', 200);
            }
            return $this->sendSuccess('CATEGORY GET SUCCESSFULLY', $cagetory_list);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductList()
    {
        try {
            $products  = Product::with('allImages')->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PROPERTY GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getSearchProduct(Request $request)
    {
        try {
            $products  = Product::with('allImages')->where('name', 'LIKE', "%" . $request->product_name . "%")->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PROPERTY GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductDetails($id)
    {
        try {
            $products  = Product::with('allImages')->where('id', $id)->first();
            // dd($products);die;
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT DETAIL GET SUCCESSFULLY', new ProductResource($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function addToCart(Request $request)
    {
        try {
            $products  = Product::find($request->product_id);
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT ID NOT FOUND', 200);
            }
            $amount = $products->kg_price * $request->product_qty;
            $checkExist = Cart::where('product_id', $request->product_id)->count();
            if ($checkExist > 0) {
            } else {
                $add_to_cart = new Cart();
                $add_to_cart->fill($request->only('product_id', 'product_qty'));
                $add_to_cart->amount = $amount;
                $add_to_cart->product_name = $products->product_name;
                $carts = auth()->user()->carts()->save($add_to_cart);
            }
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
}