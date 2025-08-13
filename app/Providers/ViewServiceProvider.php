<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Models\MenuItem; // <-- Add this
use App\Models\SidebarMenu; // <-- Add this
use App\Models\Category;    // <-- Add this
use App\Models\OffersectionSetting; // <-- Add this
use App\Models\BundleOfferProduct;  // <-- Add this
use App\Models\Product;  
use Carbon\Carbon;
class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // This composer shares data with all views in the 'front' directory.
        View::composer('front.*', function ($view) {
            // Fetch Header Settings
            $settings = Setting::pluck('value', 'key');
            $headerColor = $settings['header_color'] ?? '#FFFFFF';
            $menuLimit = $settings['menu_limit'] ?? 8; // Default to 8 if not set

            // Fetch Menu Items
            $menuItems = MenuItem::where('is_visible', true)
                                 ->orderBy('order')
                                 ->limit($menuLimit)
                                 ->get();
            
            // Pass all variables to the view
            $view->with('headerColor', $headerColor);
            $view->with('menuItems', $menuItems);
        });

        // NEW: Composer specifically for the sidebar menu
        View::composer('front.*', function ($view) {
            // Get the ordered list of categories from your sidebar control table
            $sidebarMenuItemNames = SidebarMenu::where('is_visible', true)
                                              ->orderBy('order')
                                              ->pluck('name');

            // Fetch the actual Category models with their subcategories,
            // ordered according to your admin panel settings.
            $sidebarCategories = Category::whereIn('name', $sidebarMenuItemNames)
                                         ->with(['subcategories' => function ($query) {
                                             $query->where('status', 1);
                                         }])
                                         ->get()
                                         ->sortBy(function($model) use ($sidebarMenuItemNames){
                                             return array_search($model->name, $sidebarMenuItemNames->toArray());
                                         });

            $view->with('sidebarCategories', $sidebarCategories);
        });

           View::composer('front.*', function ($view) {
            $offerSectionSetting = OffersectionSetting::with('bundleOffer')->first();
            
            $offerDeals = collect();
            $products = collect();

            if ($offerSectionSetting && $offerSectionSetting->bundleOffer) {
                $offerDeals = BundleOfferProduct::where('bundle_offer_id', $offerSectionSetting->bundleOffer->id)->get();
                $allProductIds = $offerDeals->pluck('product_id')->flatten()->unique()->all();
                $products = Product::whereIn('id', $allProductIds)->get()->keyBy('id');
            }


            // --- Timer & Date Logic ---

 
    $timezone = 'Asia/Dhaka'; // Set your application's timezone
    $now = Carbon::now($timezone);

    // Parse the 'sellEndDate' from the database and set the time to the very end of that day.
    // This creates the specific target for the countdown.
    $sellEndDateObject = Carbon::parse($offerSectionSetting->bundleOffer->enddate, $timezone)->endOfDay();

    // Initialize remaining time in case the date has passed
    $remaining = [
        'days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0
    ];

    // Only calculate the difference if the end date is in the future
    if ($now->lt($sellEndDateObject)) {
        $diff = $now->diff($sellEndDateObject);
        $remaining = [
            'days' => $diff->d,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s
        ];
    }
    
    // Get the exact end timestamp in a universal format for the client-side script
    $dealEndDateISO = $sellEndDateObject->toIso8601String();
    $view->with('dealEndDateISO', $dealEndDateISO);
    // --- Timer Logic End ---
 //dd($remaining['days'], $remaining['hours'], $remaining['minutes'], $remaining['seconds']);
            $view->with('remaining', $remaining);
            $view->with('offerSectionSetting', $offerSectionSetting);
            $view->with('offerDeals', $offerDeals);
            $view->with('offerProducts', $products); // Pass products with a different name to avoid conflicts
        });
    }
}