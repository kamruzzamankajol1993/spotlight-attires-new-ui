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
                if(is_array($deal->product_id)) {

                   // dd($deal->product_id);
                    foreach($deal->product_id as $pid) {
                        if(isset($productsCollection[$pid])) {
                            $totalBasePrice += $productsCollection[$pid]->base_price;
                        }
                    }
                }
            @endphp
            
            <a href="#">
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
                        <del class="text-muted">৳{{ number_format($totalBasePrice) }}</del>
                        <span class="fw-bold">৳{{ number_format($deal->discount_price) }}</span>
                    @else
                        <span class="fw-bold">৳{{ number_format($totalBasePrice) }}</span>
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