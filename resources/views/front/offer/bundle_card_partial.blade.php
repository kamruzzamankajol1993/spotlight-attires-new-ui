@if(count($bundleDeals)>0)

@forelse($bundleDeals as $deal)
    <div class="col">
        <div class="product-card card h-100">
            @php
                // --- 1. Get Image ---
                // Find the first valid product in the collection to use its image
                $displayProduct = null;
                if(is_array($deal->product_id)) {
                    foreach($deal->product_id as $pid) {
                        if(isset($productsCollection[$pid])) {
                            $displayProduct = $productsCollection[$pid];
                            break;
                        }
                    }
                }
                
                $image = ($displayProduct && is_array($displayProduct->main_image) && count($displayProduct->main_image) > 0)
                            ?   $front_ins_url . 'public/uploads/' . $displayProduct->main_image[0]
                            : 'https://placehold.co/400x400';

                // --- 2. Calculate Total Base Price ---
                $totalBasePrice = 0;
                if (is_array($deal->product_id)) {
                    // Determine how many products to count based on 'buy_quantity'.
                    // Default to all products in the array if 'buy_quantity' is not set or invalid.
                    $quantityToConsider = (isset($deal->buy_quantity) && $deal->buy_quantity > 0)
                                          ? (int)$deal->buy_quantity
                                          : count($deal->product_id);

                    // Get the specific number of product IDs from the start of the array.
                    $productIdsToSum = array_slice($deal->product_id, 0, $quantityToConsider);

                    // Calculate the total base price for only those products.
                    foreach ($productIdsToSum as $pid) {
                        if (isset($productsCollection[$pid])) {
                            $totalBasePrice += $productsCollection[$pid]->base_price;
                        }
                    }
                }
            @endphp
            
            <a href="{{route('offerProduct.show',$deal->id )}}">
                <img src="{{ $image }}" alt="{{ $deal->title }}" class="card-img-top img-fluid">
            </a>

            <div class="product-details-body">
                {{-- Show title from bundle_offer_product table --}}
                <h5 class="product-title mb-1">{{ $deal->title }}</h5>

                <div class="rating-stars mb-2">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
                </div>

                <p class="price-tag mb-2">
                    {{-- Show discount price from bundle_offer_product and calculated total --}}
                    @if($deal->discount_price > 0 && $deal->discount_price < $totalBasePrice)
                        <del class="text-muted">৳ {{ number_format($totalBasePrice) }}</del>
                        <span class="fw-bold">৳ {{ number_format($deal->discount_price) }}</span>
                    @else
                        <span class="fw-bold">৳ {{ number_format($totalBasePrice) }}</span>
                    @endif
                </p>
                <a href="{{route('offerProduct.show',$deal->id )}}" class="btn btn-primary btn-add-cart w-100">View Deal</a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <h4>No Deals Found</h4>
        <p>There are no special deals for this offer at the moment.</p>
    </div>
@endforelse

@else
<div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="product-card card">
                                        <img src="https://placehold.co/300x300" class="card-img-top" alt="Product 6">
                                        <div class="product-details-body">
                                            <h5 class="product-title mb-1">Product Name 6</h5>
                                            <p class="product-meta mb-1">Category: Home Goods</p>
                                            <p class="product-meta mb-1">SKU: PN-006</p>
                                            <p class="product-meta text-success fw-bold mb-1"><i
                                                    class="bi bi-check-circle-fill"></i> In stock</p>
                                            <div class="rating-stars mb-2">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                            <p class="price-tag mb-2">৳ 1100.0</p>
                                            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
@endif