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
use App\Models\AssignCategory;
use App\Models\FeaturedCategory;
use App\Models\BundleOffer;
use App\Models\Size;
use App\Models\HomepageSection; 
use App\Models\HeroLeftSlider;
use App\Models\HeroRightSlider;
use App\Models\FooterBanner;
use App\Models\ExtraCategory; 
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
    // 1. Fetch the product without the old 'category' relationship
    $product = Product::with(['variants.color'])
        ->findOrFail($id);
    
    // 2. Find the category assignment from the pivot table
    $assignedCategory = AssignCategory::where('product_id', $product->id)->first();
    
    // 3. If an assignment exists, load the full Category model
    if ($assignedCategory) {
        $category = Category::find($assignedCategory->category_id);
        if ($category) {
            // Manually set the 'category' relation on the product object
            // This allows the view to still use `$product->category`
            $product->setRelation('category', $category);
        }
    }
    
    return view('front.include.quick_view_modal_content', compact('product'));
}

     public function product($slug)
{
    // 1. Fetch the product, removing 'category' and 'subcategory' from the with() array.
    $product = Product::where('slug', $slug)
        ->with([
            'variants.color',
            'assignChart.entries',
            'reviews.user',
            'reviews.images'
        ])
        ->withCount('reviews')
        ->withAvg('reviews', 'rating')
        ->firstOrFail();

    // 2. Get all category IDs assigned to this product from the pivot table.
    $assignedCategoryIds = AssignCategory::where('product_id', $product->id)->pluck('category_id');

    if ($assignedCategoryIds->isNotEmpty()) {
        // 3. Fetch the actual Category models for all assignments, eager-loading their parents.
        $assignedCategories = Category::whereIn('id', $assignedCategoryIds)->with('parent')->get();

        // 4. Find the most specific category (the one with a parent) to use as the "subcategory".
        $subcategory = $assignedCategories->firstWhere('parent_id', '!=', null);

        // 5. Determine the parent "category".
        // If a subcategory was found, its parent is the main category.
        // Otherwise, fall back to the first assigned category.
        $category = $subcategory ? $subcategory->parent : $assignedCategories->first();
        
        // 6. Manually set the relations on the product object.
        // This allows your view to use `$product->category` and `$product->subcategory` without any changes.
        if ($category) {
            $product->setRelation('category', $category);
        }
        if ($subcategory) {
            $product->setRelation('subcategory', $subcategory);
        }
    }

    return view('front.product.show', compact('product'));
}

    public function offers()
{
    // Find the 'discount' category details to pass to the view for context
    $extraCategory = ExtraCategory::where('slug', 'discount')->firstOrFail();

    // The rest of the function remains the same...
    $getAllid = AssignCategory::where('category_name', 'discount')->pluck('product_id');

    $products = Product::where('status', 1)
        ->whereIn('id', $getAllid)
        ->with(['variants'])
        ->latest()
        ->paginate(12);

    $productIdsOnPage = $products->pluck('id');
    if ($productIdsOnPage->isNotEmpty()) {
        $assignments = AssignCategory::whereIn('product_id', $productIdsOnPage)->get()->keyBy('product_id');
        $categoryIds = $assignments->pluck('category_id')->unique();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($products as $product) {
            $assignment = $assignments->get($product->id);
            if ($assignment) {
                $category = $categories->get($assignment->category_id);
                $product->setRelation('category', $category);
            }
        }
    }
        
    $sizes = Size::where('status', 1)->get();

    // Pass the new $extraCategory variable to the view
    return view('front.discount.list', compact('products', 'sizes', 'extraCategory'));
}


public function extra_category_offer($slug)
{
    // 1. Find the details of the extra category itself (e.g., for the page title).
    $extraCategory = ExtraCategory::where('slug', $slug)->firstOrFail();

    // 2. Get all product IDs from the pivot table where 'category_name' matches the slug.
    $productIds = AssignCategory::where('category_name', $slug)->pluck('product_id');

    // 3. Fetch and paginate all products that match the retrieved IDs.
    $products = Product::whereIn('id', $productIds)
        ->where('status', 1)
        ->with('variants') // Eager load variants for efficiency
        ->latest()
        ->paginate(12);
        
    // 4. Fetch available sizes for the filter sidebar.
    $sizes = Size::where('status', 1)->get();

    // 5. Return the view, passing the products, sizes, and category details.
    // Note: You will need to create a view file at: resources/views/front/extra_category/list.blade.php
    return view('front.discount.list', compact('products', 'sizes', 'extraCategory'));
}

/**
 * NEW method dedicated to filtering ONLY discount products.
 */
public function ajaxDiscountFilter(Request $request)
{
    // Get the category slug from the request, fallback to 'discount' for safety.
    $slug = $request->input('extra_category_slug', 'discount');

    // **UPDATED LINE:** Get product IDs based on the dynamic slug.
    $productIds = AssignCategory::where('category_name', $slug)->pluck('product_id');
    
    // The main query now starts from the correct set of products.
    $query = Product::whereIn('id', $productIds)->where('status', 1);
    
    // ... (All other filter and sorting logic remains the same)
    if ($request->filled('min_price') && $request->filled('max_price')) {
        $query->whereBetween('base_price', [(float)$request->min_price, (float)$request->max_price]);
    }
    if ($request->filled('stock_status')) {
        if ($request->stock_status === 'on_sale') {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        } elseif ($request->stock_status === 'in_stock') {
            $query->whereHas('variants', fn($q) => $q->whereJsonLength('sizes', '>', 0));
        }
    }
    if ($request->filled('sizes') && is_array($request->sizes)) {
        $selectedSizeNames = $request->sizes;
        $sizeIds = Size::whereIn('name', $selectedSizeNames)->pluck('id')->toArray();
        if (!empty($sizeIds)) {
            $query->whereHas('variants', function ($variantQuery) use ($sizeIds) {
                $variantQuery->where(function ($q) use ($sizeIds) {
                    foreach ($sizeIds as $sizeId) {
                        $q->orWhereJsonContains('sizes', ['size_id' => (string)$sizeId]);
                    }
                });
            });
        }
    }
    $sortBy = $request->input('sort_by', 'newest');
    switch ($sortBy) {
        case 'price_asc':
            $query->orderByRaw('ISNULL(discount_price), discount_price ASC, base_price ASC');
            break;
        case 'price_desc':
            $query->orderByRaw('ISNULL(discount_price), discount_price DESC, base_price DESC');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'popularity':
            $query->latest();
            break;
        case 'newest':
        default:
            $query->latest();
            break;
    }
    // --- END: Filter and sort logic ---

    // Fetch paginated products, but remove the incorrect 'category' relationship.
    $products = $query->with(['variants'])->paginate(12);

    // Manually load the correct category for each product on the current page.
    $productIdsOnPage = $products->pluck('id');
    if ($productIdsOnPage->isNotEmpty()) {
        $assignments = AssignCategory::whereIn('product_id', $productIdsOnPage)->get()->keyBy('product_id');
        $categoryIds = $assignments->pluck('category_id')->unique();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($products as $product) {
            $assignment = $assignments->get($product->id);
            if ($assignment) {
                $category = $categories->get($assignment->category_id);
                $product->setRelation('category', $category);
            }
        }
    }

    $html = view('front.category.product_card_partial', compact('products'))->render();

    return response()->json([
        'html' => $html,
        'hasMorePages' => $products->hasMorePages(),
    ]);
}

     public function index()
{
    // --- START: NEW HERO SECTION LOGIC ---
    // Fetch active left sliders, ordered by latest, with their linked item (product, category, etc.)
    $heroLeftSliders = HeroLeftSlider::where('status', 1)->with('linkable')->latest()->get();

    // Fetch all active right sliders/banners with their linked items
    $allRightSliders = HeroRightSlider::where('status', 1)->with('linkable')->get();

    // Separate the right-side banners by their designated position
    $heroTopBanner = $allRightSliders->where('position', 'top')->first();
    // Assuming bottom banner positions are 'bottom_1' and 'bottom_2'
    $heroBottomBanners = $allRightSliders->whereIn('position', ['bottom_left', 'bottom_right'])->take(2);
    // --- END: NEW HERO SECTION LOGIC ---

    // Fetch Featured Category (Trending/New/Discount) sections
    $featuredCategorySettings = FeaturedCategory::pluck('value', 'key')->all();
    $titles = ExtraCategory::where('status', 1)->pluck('name', 'slug');
    
    $topProductsType = $featuredCategorySettings['first_row_category'] ?? null;
    $topProductsStatus = $featuredCategorySettings['first_row_status'] ?? false;
    $products = collect();
    $topRatedTitle = '';
    if ($topProductsStatus && $topProductsType) {
        $topRatedTitle = $titles[$topProductsType] ?? 'Top Rated Products';
        $productIds = AssignCategory::where('category_name', $topProductsType)->pluck('product_id');
        if ($productIds->isNotEmpty()) {
            $products = Product::whereIn('id', $productIds)->where('status', 1)->with(['category', 'variants'])->latest()->take(8)->get();
        }
    }
    
    $secondRowType = $featuredCategorySettings['second_row_category'] ?? null;
    $secondRowStatus = $featuredCategorySettings['second_row_status'] ?? false;
    $secondRowProducts = collect();
    $secondRowTitle = '';
    if ($secondRowStatus && $secondRowType) {
        $secondRowTitle = $titles[$secondRowType] ?? 'More For You';
        $productIds = AssignCategory::where('category_name', $secondRowType)->pluck('product_id');
        if ($productIds->isNotEmpty()) {
            $secondRowProducts = Product::whereIn('id', $productIds)->where('status', 1)->with(['category', 'variants'])->latest()->take(8)->get();
        }
    }

    // Fetch Homepage Section (Category-based) data
    // Fetch Homepage Section (Category-based) data
$homepageRow1 = HomepageSection::with('category')->where('row_identifier', 'row_1')->where('status', 1)->first();
$homepageRow2 = HomepageSection::with('category')->where('row_identifier', 'row_2')->where('status', 1)->first();

$row1Products = collect();
if ($homepageRow1 && $homepageRow1->category) {
    // Get product IDs from the assignment table for the first row's category
    $productIds = AssignCategory::where('category_id', $homepageRow1->category_id)->where('type','product_category')->pluck('product_id');
    $row1Products = Product::whereIn('id', $productIds)
        ->where('status', 1)
        ->with('variants')
        ->latest()
        ->take(8)
        ->get();
}

$row2Products = collect();
if ($homepageRow2 && $homepageRow2->category) {
    // Get product IDs from the assignment table for the second row's category
    $productIds = AssignCategory::where('category_id', $homepageRow2->category_id)->where('type','product_category')->pluck('product_id');
    $row2Products = Product::whereIn('id', $productIds)
        ->where('status', 1)
        ->with('variants')
        ->latest()
        ->take(8)
        ->get();
}

// Manually load categories for all fetched homepage products for efficiency
$allHomepageProducts = $row1Products->merge($row2Products);
$productIdsOnPage = $allHomepageProducts->pluck('id')->unique();

if ($productIdsOnPage->isNotEmpty()) {
    $assignments = AssignCategory::whereIn('product_id', $productIdsOnPage)->get()->keyBy('product_id');
    $categoryIds = $assignments->pluck('category_id')->unique();
    $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

    // This loop attaches the correct category to each product object,
    // which updates the products within both $row1Products and $row2Products collections.
    foreach ($allHomepageProducts as $product) {
        $assignment = $assignments->get($product->id);
        if ($assignment) {
            $category = $categories->get($assignment->category_id);
            if ($category) {
                // This ensures your view can still use `$product->category`
                $product->setRelation('category', $category);
            }
        }
    }
}
// --- END: UPDATED HOMEPAGE SECTION LOGIC ---

    // Fetch other necessary data for the homepage
    $featuredCategories = AnimationCategory::where('status', 1)->take(5)->get();
    $offerDeals = BundleOfferProduct::where('bundle_offer_id', 1)->get();
    $allProductIds = $offerDeals->pluck('product_id')->flatten()->unique()->all();
    $productsbun = Product::whereIn('id', $allProductIds)->get()->keyBy('id');
$footerBanner = FooterBanner::latest()->first();
    // Pass all data, including the new hero variables, to the view
    return view('front.index', compact(
        'productsbun', 'offerDeals', 'featuredCategories',
        'products', 'topRatedTitle', 'secondRowProducts', 'secondRowTitle',
        'homepageRow1', 'row1Products', 'homepageRow2', 'row2Products','footerBanner',
        'heroLeftSliders', 'heroTopBanner', 'heroBottomBanners' // <-- New variables for the hero section
    ));
}


    
/**
     * Display the initial category page with the first set of products.
     */
     public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // 1. Get all product IDs assigned to this category from the pivot table.
        $productIds = AssignCategory::where('category_id', $category->id)->where('type','product_category')->pluck('product_id');

        // 2. Fetch and paginate the products using the retrieved IDs.
        $products = Product::whereIn('id', $productIds)
            ->where('status', 1)
            ->with(['variants'])
            ->latest()
            ->paginate(12);

        // Fetch all categories and their subcategories for the filter sidebar
        $categoryList = Category::where('status', 1)->whereNull('parent_id')->with('children')->get();
        $sizes = Size::where('status', 1)->get();

        return view('front.category.category', compact('category', 'products', 'categoryList', 'sizes'));
    }

    public function productSearch(Request $request)
{
    $searchQuery = $request->input('query');

    if (!$searchQuery) {
        return redirect()->route('shop.show');
    }

    // 1. Fetch products without the incorrect 'category' relationship
    $products = Product::where('status', 1)
                       ->where('name', 'LIKE', "%{$searchQuery}%")
                       ->with(['variants']) // <-- CORRECTED
                       ->latest()
                       ->paginate(16);

    // 2. Manually load categories for the products on the current page
    $productIdsOnPage = $products->pluck('id');
    if ($productIdsOnPage->isNotEmpty()) {
        $assignments = AssignCategory::whereIn('product_id', $productIdsOnPage)->get()->keyBy('product_id');
        $categoryIds = $assignments->pluck('category_id')->unique();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($products as $product) {
            $assignment = $assignments->get($product->id);
            if ($assignment) {
                $category = $categories->get($assignment->category_id);
                if ($category) {
                    // This allows your view to still use `$product->category`
                    $product->setRelation('category', $category);
                }
            }
        }
    }

    // Fetch filter data for the sidebar
    $categoryList = Category::where('status', 1)->whereNull('parent_id')->with('children')->get();
    $animationCategoryList = AnimationCategory::where('status', 1)->get();

    return view('front.main.search_results', compact('products', 'categoryList', 'animationCategoryList', 'searchQuery'));
}

    public function shop()
{
    // 1. Fetch products without the incorrect 'category' relationship
    $products = Product::where('status', 1)
        ->with(['variants']) // <-- CORRECTED
        ->latest()
        ->paginate(12);
        
    // 2. Manually load categories for the products on the current page
    $productIdsOnPage = $products->pluck('id');
    if ($productIdsOnPage->isNotEmpty()) {
        $assignments = AssignCategory::whereIn('product_id', $productIdsOnPage)->get()->keyBy('product_id');
        $categoryIds = $assignments->pluck('category_id')->unique();
        $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

        foreach ($products as $product) {
            $assignment = $assignments->get($product->id);
            if ($assignment) {
                $category = $categories->get($assignment->category_id);
                if ($category) {
                    $product->setRelation('category', $category);
                }
            }
        }
    }

    // Fetch data for the filter sidebar
    $sizes = Size::where('status', 1)->get();
    $categoryList = Category::where('status', 1)->whereNull('parent_id')->with('children')->get();
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
             // --- START: UNIFIED CATEGORY/SUBCATEGORY FILTER ---
        // Check for either 'category_id' or 'subcategory_id' from the request.
        $categoryId = $request->input('category_id') ?: $request->input('subcategory_id');

        // If either exists, use it to filter products via the AssignCategory table.
        if ($categoryId) {
            $productIds = AssignCategory::where('category_id', $categoryId)
            ->where('type','product_category')
            ->pluck('product_id');

            $query->whereIn('id', $productIds);
        }
        // --- END: UNIFIED FILTER ---
        
        // Filter by Animation Category
        if ($request->filled('animation_category_id')) {
            $productIds = AssignCategory::where('category_id', $request->animation_category_id)
            ->where('type','animation')
            ->pluck('product_id');
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
        // 1. Find the subcategory in the main CATEGORIES table by its slug.
        // We ensure it has a parent_id to confirm it's a subcategory.
        $subcategory = Category::where('slug', $slug)->whereNotNull('parent_id')->firstOrFail();

        // 2. Get the parent category for breadcrumbs and display purposes.
        $category = $subcategory->parent;

        // 3. Get all product IDs assigned to THIS subcategory from the pivot table.
        $productIds = AssignCategory::where('category_id', $subcategory->id)->where('type','product_category')->pluck('product_id');

        // 4. Fetch and paginate the products using the retrieved IDs.
        $products = Product::whereIn('id', $productIds)
            ->where('status', 1)
            ->with(['variants'])
            ->latest()
            ->paginate(12);

        // Fetch all categories for the filter sidebar
        $categoryList = Category::where('status', 1)->with('children')->get();
        $sizes = Size::where('status', 1)->get();

        // Pass all necessary data to the original subcategory view
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
        $query = Product::where('status', 1);

        // Filter by Main Category if selected
         // --- START: UNIFIED CATEGORY/SUBCATEGORY FILTER ---
        $categoryId = $request->input('category_id') ?: $request->input('subcategory_id');


       // dd($categoryId);
        if ($categoryId) {
            $productIds = AssignCategory::where('category_id', $categoryId)
            ->where('type','product_category')->pluck('product_id');
            $query->whereIn('id', $productIds);
        }
        // --- END: UNIFIED FILTER ---
        
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

        $products = $query->with(['category', 'variants'])->paginate(12);

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
            ->where('type', 'animation') 
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
    $productIds = AssignCategory::where('category_id', $request->animation_category_id)->where('type','aimation')->pluck('product_id');

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
