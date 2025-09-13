<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AnimationCategory;
use App\Models\BundleOfferProduct;
use App\Models\SliderControl;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\HighlightProduct;

use App\Models\BundleOffer;
use App\Models\AssignCategory;
use App\Models\Size;
class FrontController extends Controller
{


      /**
     * Handle AJAX search requests for products.
     */
    public function ajaxSearch(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 1) {
            return response()->json([]);
        }


        $frontEndData = DB::table('system_information')->first();

        

        $products = Product::where('status', 1)
                           ->where('name', 'LIKE', "{$query}%")
                           ->select('name', 'slug', 'main_image', 'base_price', 'discount_price')
                           ->take(10) // Limit the number of results
                           ->get();

        // Prepare the data for the frontend, including image and product URLs
        $products->transform(function ($product) use ($frontEndData) {
            $imageUrl = (is_array($product->main_image) && count($product->main_image) > 0)
                ? $frontEndData->main_url . 'public/uploads/' . $product->main_image[0]
                : 'https://placehold.co/50x50?text=N/A';
            
            $product->image_url = $imageUrl;
            $product->url = route('product.show', $product->slug);
            return $product;
        });

        return response()->json($products);
    }


     public function offerProduct($id)
    {
        $bundleDeal = BundleOfferProduct::findOrFail($id);
        $productIds = $bundleDeal->product_id;
        $productsCollection = collect();
        $allImages = [];
        $totalBasePrice = 0;

        if (!empty($productIds) && is_array($productIds)) {
            // --- UPDATED QUERY ---
            // Eager load all necessary relationships for the products in the bundle
            $productsCollection = Product::whereIn('id', $productIds)
                ->with([
                    'variants.color', 
                    'reviews.user', // Eager load approved reviews and the user who wrote them
                    'reviews.images'  // Eager load images for each review
                ])
                ->withCount('reviews') // Get the total number of reviews for each product
                ->withAvg('reviews', 'rating') // Calculate the average rating for each product
                ->get();
            // --- END UPDATED QUERY ---

            foreach ($productsCollection as $product) {
                if (is_array($product->main_image) && count($product->main_image) > 0) {
                    $allImages = array_merge($allImages, $product->main_image);
                }
                $totalBasePrice += $product->base_price;
            }
        }

        $allImages = array_unique($allImages);

        return view('front.offer.offerproduct', compact(
            'bundleDeal',
            'productsCollection',
            'allImages',
            'totalBasePrice'
        ));
    }

    public function quickView($id)
{
    $product = Product::with(['variants.color', 'category'])
        ->findOrFail($id);
    
    // We will create this new view file in the next step
    return view('front.include.quick_view_modal_content', compact('product'));
}

     public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->with([
                'category',                 // For breadcrumbs
                'subcategory',              // For breadcrumbs
                'variants.color',           // Eager load variants AND their associated colors
                'assignChart.entries',       // Eager load the assigned size chart AND its entries
                'reviews.user', // Eager load approved reviews and the user who wrote them
                'reviews.images'
            ])
             ->withCount('reviews') // Get the total number of reviews
            ->withAvg('reviews', 'rating') // Calculate the average rating directly in the query
            ->firstOrFail();

        return view('front.product.show', compact('product'));
    }

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

        // Fetch Highlight Products
        $firstHighlight = HighlightProduct::with('product.category')->where('section', 'first_section')->first();
        $secondHighlight = HighlightProduct::with('product.category')->where('section', 'second_section')->first();

        return view('front.index', compact('firstHighlight', 'secondHighlight', 'productsbun', 'offerDeals', 'latestProducts', 'topBannerProduct', 'bottomBannerProducts', 'products', 'randomLatestProducts', 'randomProducts', 'featuredCategories'));
    }


    
/**
     * Display the initial category page with the first set of products.
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Load the initial batch of products (first page)
        $products = Product::where('category_id', $category->id)
            ->where('status', 1)
            ->with(['variants'])
            ->latest()
            ->paginate(12); // Use pagination

        // Fetch all categories and their subcategories for the filter sidebar
        $categoryList = Category::where('status', 1)->with('subcategories')->get();

        $sizes = Size::where('status', 1)->get();

        return view('front.category.category', compact('category', 'products', 'categoryList', 'sizes'));
    }

    public function productSearch(Request $request)
    {
        $searchQuery = $request->input('query');

        if (!$searchQuery) {
            return redirect()->route('shop.show');
        }

        $products = Product::where('status', 1)
                           ->where('name', 'LIKE', "%{$searchQuery}%")
                           ->with(['category', 'variants'])
                           ->latest()
                           ->paginate(16);

        // Fetch filter data for a potential sidebar on the search page
        $categoryList = Category::where('status', 1)->with('subcategories')->get();
        $animationCategoryList = AnimationCategory::where('status', 1)->get();

        return view('front.main.search_results', compact('products', 'categoryList', 'animationCategoryList', 'searchQuery'));
    }

    public function shop()
    {
        $products = Product::where('status', 1)
            ->with(['category', 'variants'])
            ->latest()
            ->paginate(12);
        // --- NEW: Fetch all available sizes for the filter ---
        $sizes = Size::where('status', 1)->get();

        // Fetch all filter groups for the sidebar
        $categoryList = Category::where('status', 1)->with('subcategories')->get();
        $animationCategoryList = AnimationCategory::where('status', 1)->get();

        return view('front.main.shop', compact('products', 'categoryList', 'animationCategoryList', 'sizes'));
    }

     /**
     * NEW dedicated function to handle AJAX filter requests for the shop page.
     */
    public function ajaxShopFilter(Request $request)
    {
        $query = Product::where('status', 1);

        // Filter by Category or Subcategory
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }
        
        // Filter by Animation Category
        if ($request->filled('animation_category_id')) {
            $productIds = AssignCategory::where('category_id', $request->animation_category_id)->pluck('product_id');
            $query->whereIn('id', $productIds);
        }

        // Filter by Price Range
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('base_price', [(float)$request->min_price, (float)$request->max_price]);
        }

        // Filter by Stock Status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'on_sale') {
                $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereHas('variants', fn($q) => $q->whereJsonLength('sizes', '>', 0));
            }
        }

          // --- START: CORRECTED SIZE FILTER LOGIC ---
        if ($request->filled('sizes') && is_array($request->sizes)) {
            $selectedSizeNames = $request->sizes;

            // Step 1: Get the IDs for the selected size names from the 'sizes' table.
            $sizeIds = Size::whereIn('name', $selectedSizeNames)->pluck('id')->toArray();

            if (!empty($sizeIds)) {
                $query->whereHas('variants', function ($variantQuery) use ($sizeIds) {
                    // Step 2: Check if the 'sizes' JSON column in the 'product_variants' table
                    // contains an object with any of the found 'size_id's.
                    $variantQuery->where(function ($q) use ($sizeIds) {
                        foreach ($sizeIds as $sizeId) {
                            // IMPORTANT: The 'size_id' in your JSON is a string, so we cast our integer ID to a string for a correct match.
                            $q->orWhereJsonContains('sizes', ['size_id' => (string)$sizeId]);
                        }
                    });
                });
            }
        }
        // --- END: CORRECTED SIZE FILTER LOGIC ---

         // --- START: NEW SORTING LOGIC ---
        $sortBy = $request->input('sort_by', 'newest'); // Default to 'newest'

        switch ($sortBy) {
            case 'price_asc':
                // Order by discount price if it exists, otherwise by base price
                $query->orderByRaw('ISNULL(discount_price), discount_price ASC, base_price ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('ISNULL(discount_price), discount_price DESC, base_price DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'popularity':
                // NOTE: 'popularity' requires a metric like sales or views.
                // As a placeholder, we'll sort by newest.
                $query->latest();
                break;
            case 'newest':
            default:
                $query->latest(); // This is equivalent to orderBy('created_at', 'desc')
                break;
        }
        // --- END: NEW SORTING LOGIC ---

        $products = $query->with(['category', 'variants'])->latest()->paginate(12);
//dd($products);
        $html = view('front.category.product_card_partial', compact('products'))->render();

        return response()->json([
            'html' => $html,
            'hasMorePages' => $products->hasMorePages(),
        ]);
    }

        public function subcategory($slug)
    {
        $subcategory = Subcategory::where('slug', $slug)->firstOrFail();
        
        // Load the initial batch of products for this subcategory
        $products = Product::where('subcategory_id', $subcategory->id)
            ->where('status', 1)
            ->with(['variants'])
            ->latest()
            ->paginate(12);

        // Fetch all categories for the filter sidebar
        // We pass the parent category to the view to help expand the sidebar correctly
        $category = $subcategory->category;
        $categoryList = Category::where('status', 1)->with('subcategories')->get();
        $sizes = Size::where('status', 1)->get();

        return view('front.category.subcategory', compact('subcategory', 'category', 'products', 'categoryList', 'sizes'));
    }


/**
     * MODIFIED: Display the initial offer page with its bundle deals.
     */
    public function offer($slug)
    {
        $offer = BundleOffer::where('slug', $slug)->firstOrFail();

        // 1. Paginate the actual bundle deals for this offer
        $bundleDeals = BundleOfferProduct::where('bundle_offer_id', $offer->id)->paginate(12);

        // 2. Get all unique product IDs from the current page of deals
        $allProductIds = $bundleDeals->pluck('product_id')->flatten()->unique()->all();

        // 3. Fetch all related products in a single query for efficiency
        $productsCollection = Product::whereIn('id', $allProductIds)->get()->keyBy('id');

        // Fetch all active offers for the filter sidebar
        $offerList = BundleOffer::where('status', 1)->where('enddate', '>=', now())->get();

        return view('front.offer.show', compact('offer', 'bundleDeals', 'productsCollection', 'offerList'));
    }

    /**
     * MODIFIED: Handle AJAX requests to filter and display bundle deals.
     */
    public function filterOffers(Request $request)
{
    $request->validate(['offer_id' => 'required|integer|exists:bundle_offers,id']);

    // Start the query for bundle deals
    $query = BundleOfferProduct::where('bundle_offer_id', $request->offer_id);

    // MODIFIED: Add the price range filter to the query
    if ($request->filled('min_price') && $request->filled('max_price')) {
        $query->whereBetween('discount_price', [(float)$request->min_price, (float)$request->max_price]);
    }

    // Paginate the results
    $bundleDeals = $query->paginate(12);

    // The rest of the method remains the same
    $allProductIds = $bundleDeals->pluck('product_id')->flatten()->unique()->all();
    $productsCollection = Product::whereIn('id', $allProductIds)->get()->keyBy('id');
    $html = view('front.offer.bundle_card_partial', compact('bundleDeals', 'productsCollection'))->render();

    return response()->json([
        'html' => $html,
        'hasMorePages' => $bundleDeals->hasMorePages(),
    ]);
}


    /**
     * Handle AJAX requests for filtering and loading more products.
     */
    public function filterProducts(Request $request)
    {

       // dd(12);
        // Start with a broad query for all products.
        $query = Product::where('status', 1)->with(['category', 'variants']);

        // Filter by Main Category if selected
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by Subcategory if selected
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }
        
        // Filter by Price Range
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('base_price', [(float)$request->min_price, (float)$request->max_price]);
        }

        // Filter by Stock Status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'on_sale') {
                $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereHas('variants', function ($variantQuery) {
                    $variantQuery->whereJsonLength('sizes', '>', 0);
                });
            }
        }

          // --- START: CORRECTED SIZE FILTER LOGIC ---
        if ($request->filled('sizes') && is_array($request->sizes)) {
            $selectedSizeNames = $request->sizes;

            // Step 1: Get the IDs for the selected size names from the 'sizes' table.
            $sizeIds = Size::whereIn('name', $selectedSizeNames)->pluck('id')->toArray();

            if (!empty($sizeIds)) {
                $query->whereHas('variants', function ($variantQuery) use ($sizeIds) {
                    // Step 2: Check if the 'sizes' JSON column in the 'product_variants' table
                    // contains an object with any of the found 'size_id's.
                    $variantQuery->where(function ($q) use ($sizeIds) {
                        foreach ($sizeIds as $sizeId) {
                            // IMPORTANT: The 'size_id' in your JSON is a string, so we cast our integer ID to a string for a correct match.
                            $q->orWhereJsonContains('sizes', ['size_id' => (string)$sizeId]);
                        }
                    });
                });
            }
        }
        // --- END: CORRECTED SIZE FILTER LOGIC ---

         // --- START: NEW SORTING LOGIC ---
        $sortBy = $request->input('sort_by', 'newest'); // Default to 'newest'

        switch ($sortBy) {
            case 'price_asc':
                // Order by discount price if it exists, otherwise by base price
                $query->orderByRaw('ISNULL(discount_price), discount_price ASC, base_price ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('ISNULL(discount_price), discount_price DESC, base_price DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'popularity':
                // NOTE: 'popularity' requires a metric like sales or views.
                // As a placeholder, we'll sort by newest.
                $query->latest();
                break;
            case 'newest':
            default:
                $query->latest(); // This is equivalent to orderBy('created_at', 'desc')
                break;
        }
        // --- END: NEW SORTING LOGIC ---

        $products = $query->latest()->paginate(12);

        $html = view('front.category.product_card_partial', compact('products'))->render();

        return response()->json([
            'html' => $html,
            'hasMorePages' => $products->hasMorePages(),
        ]);

    }

     public function animationCategory($slug)
    {
        $animationCategory = AnimationCategory::where('slug', $slug)->firstOrFail();

        // 1. Get all product IDs assigned to this animation category
        $productIds = AssignCategory::where('category_id', $animationCategory->id)
            // You might add ->where('type', 'animation') if you have other types
            ->pluck('product_id');

        // 2. Fetch and paginate the initial products
        $products = Product::whereIn('id', $productIds)
            ->where('status', 1)
            ->with(['variants'])
            ->latest()
            ->paginate(12);

        // 3. Fetch all active animation categories for the filter sidebar
        $animationCategoryList = AnimationCategory::where('status', 1)->get();
        $sizes = Size::where('status', 1)->get();

        return view('front.animation.show', compact('animationCategory', 'products', 'animationCategoryList', 'sizes'));
    }

    public function filterAnimationCategory(Request $request)
{
    $request->validate(['animation_category_id' => 'required|integer|exists:animation_categories,id']);

    // 1. Get product IDs for the requested animation category
    $productIds = AssignCategory::where('category_id', $request->animation_category_id)->pluck('product_id');

    // 2. Start the query for products
    $productsQuery = Product::whereIn('id', $productIds)->where('status', 1);

    // 3. MODIFIED: Add Price and Stock filters to the product query
    if ($request->filled('min_price') && $request->filled('max_price')) {
        $productsQuery->whereBetween('base_price', [(float)$request->min_price, (float)$request->max_price]);
    }

    if ($request->filled('stock_status')) {
        if ($request->stock_status === 'on_sale') {
            $productsQuery->whereNotNull('discount_price')->where('discount_price', '>', 0);
        } elseif ($request->stock_status === 'in_stock') {
            $productsQuery->whereHas('variants', function ($variantQuery) {
                $variantQuery->whereJsonLength('sizes', '>', 0);
            });
        }
    }

      // --- START: CORRECTED SIZE FILTER LOGIC ---
        if ($request->filled('sizes') && is_array($request->sizes)) {
            $selectedSizeNames = $request->sizes;

            // Step 1: Get the IDs for the selected size names from the 'sizes' table.
            $sizeIds = Size::whereIn('name', $selectedSizeNames)->pluck('id')->toArray();

            if (!empty($sizeIds)) {
                $query->whereHas('variants', function ($variantQuery) use ($sizeIds) {
                    // Step 2: Check if the 'sizes' JSON column in the 'product_variants' table
                    // contains an object with any of the found 'size_id's.
                    $variantQuery->where(function ($q) use ($sizeIds) {
                        foreach ($sizeIds as $sizeId) {
                            // IMPORTANT: The 'size_id' in your JSON is a string, so we cast our integer ID to a string for a correct match.
                            $q->orWhereJsonContains('sizes', ['size_id' => (string)$sizeId]);
                        }
                    });
                });
            }
        }
        // --- END: CORRECTED SIZE FILTER LOGIC ---

    // --- START: NEW SORTING LOGIC ---
        $sortBy = $request->input('sort_by', 'newest'); // Default to 'newest'

        switch ($sortBy) {
            case 'price_asc':
                $productsQuery->orderByRaw('ISNULL(discount_price), discount_price ASC, base_price ASC');
                break;
            case 'price_desc':
                $productsQuery->orderByRaw('ISNULL(discount_price), discount_price DESC, base_price DESC');
                break;
            case 'name_asc':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'popularity':
                // As a placeholder, we'll sort by newest.
                $productsQuery->latest();
                break;
            case 'newest':
            default:
                $productsQuery->latest(); // orderBy('created_at', 'desc')
                break;
        }
        // --- END: NEW SORTING LOGIC ---

    // 4. Paginate the final results
    $products = $productsQuery->with(['variants'])->latest()->paginate(12);

    // The rest of the method is unchanged
    $html = view('front.category.product_card_partial', compact('products'))->render();

    return response()->json([
        'html' => $html,
        'hasMorePages' => $products->hasMorePages(),
    ]);
}
    
}
