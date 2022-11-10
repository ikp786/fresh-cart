<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CartListCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\ProductResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function userDashboard()
    {
        try {
            $cagetory_list = Category::OrderBy('name', 'asc')->where('status',1)->get(['id', 'name', 'image']);
            $profile_pic   = !empty(auth()->user()->profile_pic) ? asset('storage/app/public/user_images/' . auth()->user()->profile_pic) : asset('storage/user_images/logo.png');
            $get_address   = Address::where('user_id', auth()->user()->id)->where('is_favorite', 1)->first();
            $profile       = [
                'name'           => auth()->user()->name,
                'address'        => isset($get_address->address) ? $get_address->address : '',
                'address_type'        => isset($get_address->type) ? $get_address->type : '',
                'profile_pic'    => $profile_pic
            ];
            $products  = Product::where('freshfromthefarm', '1')->where('status',1)->with('allImages')->limit(10)->get();
            $sliders = Slider::get();
            return $this->sendSuccess('DASHBOARD GET SUCCESSFULLY', ['category' => CategoryResource::collection($cagetory_list), 'product_details' => ProductResource::collection($products), 'user_data' => $profile, 'banner' => BannerResource::collection($sliders)]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // PRODUCT CATEGORY LIST
    public function getCategoryList()
    {
        try {
            $cagetory_list = Category::OrderBy('name', 'asc')->where('status',1)->get(['id', 'name', 'image']);
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('CATEGORY NOT FOUND', 200);
            }
            return $this->sendSuccess('CATEGORY GET SUCCESSFULLY', CategoryResource::collection($cagetory_list));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // OFFER API
    public function getOfferList()
    {
        try {
            $offer_list  = Offer::where('status', 1)->with('products', 'products.images')->OrderBy('id', 'desc')->limit(1)->get();
            if (!isset($offer_list) || count($offer_list) == 0) {
                return $this->sendFailed('OFFER NOT FOUND', 200);
            }
            return $this->sendSuccess('OFFER GET SUCCESSFULLYY', OfferResource::collection($offer_list));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductFreshFarmList()
    {
        try {
            $products  = Product::where('freshfromthefarm','1')->with('allImages')->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductCategoryWise($cate_id)
    {
        try {
            $products  = Product::where('category_id', $cate_id)->with('allImages')->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getSearchProduct(Request $request)
    {
        try {
            if ($request->product_name == '') {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            $products  = Product::with('allImages')->where('name', 'LIKE', "%" . $request->product_name . "%")->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductDetails($id)
    {
        try {
            $products  = Product::with('allImages')->where('id', $id)->first();
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
            // CHEK THIS PRODUCT ALREADY ADDED OR NOT IN CART
            $checkExist = Cart::where(['product_id' => $request->product_id, 'user_id' => auth()->user()->id])->first();
            // UPDATE PRODUCT IN CART
            if (!empty($checkExist)) {
                $product_phav_amount       = $request->product_quantity_phav * $products->pav_price;
                $product_half_kg_amount    = $request->product_quantity_half_kg * $products->half_kg_price;
                $product_kg_amount         = $request->product_quantity_kg * $products->kg_price;
                $product_total_quantity    = $request->product_quantity_phav + $request->product_quantity_half_kg + $request->product_quantity_kg;
                $add_to_cart =  Cart::find($checkExist->id);
                if ($request->product_quantity_phav != '') {
                    $add_to_cart->product_quantity_phav = $request->product_quantity_phav;
                    $add_to_cart->product_phav_amount    = $product_phav_amount;
                }
                if ($request->product_quantity_half_kg != '') {
                    $add_to_cart->product_quantity_half_kg = $request->product_quantity_half_kg;
                    $add_to_cart->product_half_kg_amount = $product_half_kg_amount;
                }
                if ($request->product_quantity_kg != '') {
                    $add_to_cart->product_quantity_kg = $request->product_quantity_kg;
                    $add_to_cart->product_kg_amount      = $product_kg_amount;
                }
                $carts = auth()->user()->carts()->save($add_to_cart);
                // UPDATE TOTAL AMOUNT THIS PRODUCT ADDED IN CART AND UPDATE TOTAL QUANTITY PRODUCT IN CART
                $add_to_cart2 =  Cart::find($checkExist->id);
                $add_to_cart2->total_amount           = $add_to_cart2->product_phav_amount + $add_to_cart2->product_half_kg_amount + $add_to_cart2->product_kg_amount;
                $add_to_cart2->product_total_quantity    = $add_to_cart2->product_quantity_phav + $add_to_cart2->product_quantity_half_kg + $add_to_cart2->product_quantity_kg;
                $carts = auth()->user()->carts()->save($add_to_cart2);
            } else {
                // ADD PRODUCT IN CART
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
            }

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
    {
        try {
            $get_cart_data  = Cart::where(['user_id' => auth()->user()->id])->get();
            if (!isset($get_cart_data) || count($get_cart_data) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
            }

            $cart_sum = auth()->user()->carts->sum('total_amount');

            $checkCarts = Cart::where('user_id', auth()->user()->id)->get()->toArray();
            $total_amount = 0;
            foreach ($checkCarts as $key => $val) {
                $productsData           = Product::where('id', $val['product_id'])->first();
                $pav_price              = (int)$productsData->pav_price      * (int)$val['product_quantity_phav'];
                $half_kg_price          = (int)$productsData->half_kg_price  * (int)$val['product_quantity_half_kg'];
                $kg_price               = (int)$productsData->kg_price       * (int)$val['product_quantity_kg'];
                $single_product_price   = $pav_price + $half_kg_price + $kg_price;
                $total_amount           = $total_amount + $single_product_price;
            }

            $delivery_charge = Setting::first();
            $offer_list  = Offer::where('status', 1)->with('products', 'products.images')->OrderBy('id', 'desc')->limit(1)->get();
            $total_sum_with_delivery_charg = $delivery_charge->deliver_charge + $total_amount;

            return $this->sendSuccess('CART DATA GET SUCCESSFULLY', ['cart_data' => CartListCollection::collection($get_cart_data), 'offer' => OfferResource::collection($offer_list), 'delivery_charge' => $delivery_charge->deliver_charge, 'total_product_price' => $total_amount, 'total_sum_with_delivery_charg' => $total_sum_with_delivery_charg]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
}
