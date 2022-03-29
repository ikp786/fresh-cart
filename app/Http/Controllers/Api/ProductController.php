<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartListCollection;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Offer;
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

    // OFFER API
    public function getOfferList()
    {
        try {
            $cagetory_list = Offer::OrderBy('id', 'desc')->get();
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('OFFER NOT FOUND', 200);
            }
            return $this->sendSuccess('OFFER GET SUCCESSFULLY', $cagetory_list);
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

    function addToCart(AddToCartRequest $request)
    {

        try {
            $products  = Product::find($request->product_id);
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT ID NOT FOUND', 200);
            }
            $checkExist = Cart::where(['product_id' => $request->product_id, 'user_id' => auth()->user()->id])->first();
            if (!empty($checkExist)) {
                $checkExist->delete();
            }
            $product_phav_amount       = $request->product_quantity_phav * $products->pav_price;
            $product_half_kg_amount    = $request->product_quantity_half_kg * $products->half_kg_price;
            $product_kg_amount         = $request->product_quantity_kg * $products->kg_price;
            $product_total_quantity    = $request->product_quantity_phav + $request->product_quantity_half_kg + $request->product_quantity_kg;
            $add_to_cart = new Cart();
            $add_to_cart->fill($request->only('product_id', 'product_quantity_phav', 'product_quantity_half_kg', 'product_quantity_kg'));
            $add_to_cart->product_total_quantity = $product_total_quantity;
            $add_to_cart->product_phav_amount    = $product_phav_amount;
            $add_to_cart->product_half_kg_amount = $product_half_kg_amount;
            $add_to_cart->product_kg_amount      = $product_kg_amount;
            $add_to_cart->total_amount           = $product_phav_amount + $product_half_kg_amount + $product_kg_amount;
            $add_to_cart->product_name           = $products->name;
            $carts = auth()->user()->carts()->save($add_to_cart);

            return $this->sendSuccess('PRODCUT ADDED IN CARD SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // PRODUCT DELETE IN CART
    public function deleteProdcutInCart($id)
    {
        try {
            $checkExist = Cart::find($id);
            if (!$checkExist) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            $checkExist->delete();
            return $this->sendSuccess('PRODUCT DELETE IN CART SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getCartDetail()
    { {
            try {
                $get_cart_data  = Cart::where(['user_id' => auth()->user()->id])->get();
                if (!isset($get_cart_data) || count($get_cart_data) == 0) {
                    return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
                }
                return $this->sendSuccess('CART DATA GET SUCCESSFULLY', CartListCollection::collection($get_cart_data));
            } catch (\Throwable $e) {
                return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
            }
        }
    }
}
