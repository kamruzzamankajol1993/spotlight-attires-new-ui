{{-- This partial view contains the loop for rendering product cards --}}
@forelse ($products as $product)
    <div class="col">
        <div class="product-card card h-100">
            @php
                $mobileImage = (is_array($product->thumbnail_image) && count($product->thumbnail_image) > 0)
                                ?  $front_ins_url . 'public/uploads/' . $product->thumbnail_image[0]
                                : 'https://placehold.co/400x400';
                $desktopImage = (is_array($product->main_image) && count($product->main_image) > 0)
                                ?  $front_ins_url . 'public/uploads/' . $product->main_image[0]
                                : 'https://placehold.co/400x400';

                // Calculate total stock from all variants
                $totalStock = 0;
                if ($product->variants->isNotEmpty()) {
                    foreach ($product->variants as $variant) {
                        if (is_array($variant->sizes)) {
                            foreach ($variant->sizes as $sizeInfo) {
                                $totalStock += $sizeInfo['quantity'] ?? 0;
                            }
                        }
                    }
                }
            @endphp
            
            <a href="#"> {{-- Add product details route later --}}
                <picture>
                    <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
                    <img src="{{ $mobileImage }}" alt="{{ $product->name }}" class="card-img-top img-fluid">
                </picture>
            </a>

            <div class="product-details-body">
                <h5 class="product-title mb-1">{{ Str::limit($product->name, 25) }}</h5>
                <p class="product-meta mb-1">SKU: {{ $product->product_code ?? 'N/A' }}</p>

                @if($totalStock > 0)
                    <p class="product-meta text-success fw-bold mb-1"><i class="bi bi-check-circle-fill"></i> In stock</p>
                @else
                    <p class="product-meta text-danger fw-bold mb-1"><i class="bi bi-x-circle-fill"></i> Out of stock</p>
                @endif

                <div class="rating-stars mb-2">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                </div>

                <p class="price-tag mb-2">
                    @if($product->discount_price)
                        <del class="text-muted">৳{{ number_format($product->base_price) }}</del>
                        <span class="fw-bold">৳{{ number_format($product->discount_price) }}</span>
                    @else
                        <span class="fw-bold">৳{{ number_format($product->base_price) }}</span>
                    @endif
                </p>
                <a href="#" class="btn btn-primary btn-add-cart w-100">Add to Cart</a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <h4>No Products Found</h4>
        <p>Try adjusting your filters to find what you're looking for.</p>
    </div>
@endforelse