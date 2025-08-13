<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AnimationCategory;
use App\Models\BundleOfferProduct;
use App\Models\SliderControl;
class FrontController extends Controller
{
    public function index()
    {


        $getMainPreoductIds = SliderControl::where('section_key', 'main_slider')
            ->where('is_visible', 1)
            ->pluck('product_ids')
            ->flatten()
            ->unique()
            ->all();

            $getMainPreoductIdsLast = SliderControl::where('section_key', 'bottom_banners')
            ->where('is_visible', 1)
            ->pluck('product_ids')
            ->flatten()
            ->unique()
            ->all();

             $getMainTopBannerProduct = SliderControl::where('section_key', 'top_banner')
            ->where('is_visible', 1)
            ->pluck('product_ids')
            ->flatten()
            ->unique()
            ->all();


         // Fetch the latest 2 products from the database
        $latestProducts = Product::where('status', 1)
        ->whereIn('id', $getMainPreoductIds) // Filter by the product IDs from the slider
                                 ->latest() // Orders by created_at descending
                               
                                 ->get();

                                  // Query 1: For the top banner (skip 2, take the 3rd)
        $topBannerProduct = Product::where('id',(int) $getMainTopBannerProduct[0])->where('status', 1)->first();

        // Query 2: For the bottom two banners (skip the next 3 to get the 7th and 8th)
        // We skip a total of 6 (2 from slider + 1 from top banner + 3 specified)
        $bottomBannerProducts = Product::where('status', 1) ->whereIn('id', $getMainPreoductIdsLast)->latest()->get();

          $products = Product::where('status', 1)
                           ->with(['category', 'variants']) // Eager load relationships
                           ->latest()
                           ->skip(5)
                           ->take(6)
                           ->get();

                            // Query for 5 random latest products
        $randomLatestProducts = Product::where('status', 1)
                                       ->with(['category', 'variants']) // Eager load relationships
                                       ->latest() // Get the most recent ones first
                                       ->take(20) // Take a pool of the latest 20 products
                                       ->get()
                                       ->random(5); // Pick 5 randomly from that pool
                                       // Query for 5 random products from the entire list
        $randomProducts = Product::where('status', 1)
                                 ->with(['category', 'variants']) // Eager load relationships
                                 ->inRandomOrder() // Efficiently get random rows
                                 ->take(5)
                                 ->get();

                                 $featuredCategories = AnimationCategory::where('status', 1)
                                            
                                               ->take(5)
                                               ->get();
    // 1. Fetch all deal records for a specific main offer (e.g., where bundle_offer_id is 1)
        $offerDeals = BundleOfferProduct::where('bundle_offer_id', 1)->get();

        // 2. Get all unique product IDs from all the deals
        $allProductIds = $offerDeals->pluck('product_id')->flatten()->unique()->all();

        // 3. Fetch all the product models for those IDs in a single query
        $productsbun = Product::whereIn('id', $allProductIds)->get()->keyBy('id');

        return view('front.index', compact('productsbun','offerDeals','latestProducts', 'topBannerProduct', 'bottomBannerProducts', 'products', 'randomLatestProducts', 'randomProducts', 'featuredCategories'));
    }
}
