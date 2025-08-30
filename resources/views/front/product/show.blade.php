@extends('front.master.master')

@section('title')
{{ $product->name }}
@endsection
@section('css')
<style>
    /* Style for color swatches */
    .color-option {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid #ddd;
        transition: transform 0.2s, border-color 0.2s;
        display: inline-block;
    }
    .color-option.active {
        border-color: #000;
        transform: scale(1.15);
        box-shadow: 0 0 8px rgba(0,0,0,0.3);
    }
    /* Style for size buttons */
    .size-option.active {
        background-color: #212529 !important;
        color: #fff !important;
        border-color: #212529 !important;
    }
    .size-option:disabled {
        cursor: not-allowed;
        opacity: 0.5;
        text-decoration: line-through;
    }
</style>
@endsection
@section('body')
<main>
        <section class="section">
            <div class="container">
                <div class="product-container container my-5 p-4 bg-white rounded-4 shadow">

                    <!-- Product Page Header -->
                    <div class="d-flex align-items-center text-muted small mb-4">
                         <a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
                    @if($product->category)
                        <span class="mx-2">/</span>
                        <a href="{{ route('category.show', $product->category->slug) }}" class="text-muted text-decoration-none">{{ $product->category->name }}</a>
                    @endif
                    @if($product->subcategory)
                        <span class="mx-2">/</span>
                        <a href="{{ route('subcategory.show', $product->subcategory->slug) }}" class="text-muted text-decoration-none">{{ $product->subcategory->name }}</a>
                    @endif
                    <span class="mx-2">/</span>
                    <span class="fw-semibold text-dark">{{ $product->name }}</span>
                    </div>

                    <div class="row g-4">
                        <!-- Left Side: Image Gallery -->
                        <div class="col-12 col-lg-6 d-flex">
                            <div class="d-flex flex-column align-items-center">
                                <!-- Thumbnail slider for navigation -->
                                <div id="thumbnail-nav-slider" class="w-100" style="max-width: 100px;">
                                    @php
                                    // Combine product and first variant images for initial display
                                    $initialThumbnails = $product->thumbnail_image ?? [];
                                    $firstVariant = $product->variants->first();
                                    if ($firstVariant && is_array($firstVariant->variant_image)) {
                                        $initialThumbnails = array_merge($initialThumbnails, $firstVariant->variant_image);
                                    }
                                @endphp
                                @forelse ($initialThumbnails as $thumb)
                                    <div><img src="{{ $front_ins_url . 'public/uploads/' . $thumb }}" alt="Thumbnail Image" class="img-fluid rounded-3 thumbnail-image"></div>
                                @empty
                                    <div><img src="https://placehold.co/100x100/F5F5F5/4B5563?text=No+Image" alt="No Thumbnail" class="img-fluid rounded-3"></div>
                                @endforelse
                                </div>
                                <!-- Custom arrow controls placed below the thumbnail column -->
                                <div class="custom-arrows d-flex justify-content-start align-items-center w-100 mt-2">
                                    <button class="prev-arrow"><i class="bi bi-chevron-up"></i></button>
                                    <button class="next-arrow"><i class="bi bi-chevron-down"></i></button>
                                </div>
                            </div>

                            <!-- Main image slider -->
                            <div id="main-product-slider" class="flex-grow-1 rounded-3 overflow-hidden ms-4">
                                 @php
                                $initialMainImages = $product->main_image ?? [];
                                if ($firstVariant && is_array($firstVariant->main_image)) {
                                    $initialMainImages = array_merge($initialMainImages, $firstVariant->main_image);
                                }
                            @endphp
                            @forelse ($initialMainImages as $image)
                                <div><img src="{{ $front_ins_url . 'public/uploads/' . $image }}" alt="{{ $product->name }}" class="img-fluid rounded-3"></div>
                            @empty
                                <div><img src="https://placehold.co/1000x1000/F5F5F5/4B5563?text=No+Image" alt="No Product Image" class="img-fluid rounded-3"></div>
                            @endforelse
                            </div>
                        </div>

                        <!-- Right Side: Product Details -->
                        <div class="col-12 col-lg-6 d-flex flex-column p-4 spotlight_product_details">
                            <h1 class="h3 fw-semibold text-dark mb-2">{{ $product->name }}</h1>
                            <p class="h6 text-muted mb-4">SKU: <span id="product-sku">{{ $product->product_code }}</span></p>
                            <div class="d-flex align-items-baseline mb-4">
                                @if($product->discount_price)
                                <span class="h3 fw-bold text-dark" id="product-price">৳ {{ number_format($product->discount_price, 2) }}</span>
                                <span class="h6 text-muted text-decoration-line-through ms-2" id="product-base-price">৳ {{ number_format($product->base_price, 2) }}</span>
                                @php
                                    $discountPercentage = round((($product->base_price - $product->discount_price) / $product->base_price) * 100);
                                @endphp
                                <span class="ms-4 badge bg-danger fw-bold">-{{ $discountPercentage }}% OFF</span>
                            @else
                                <span class="h3 fw-bold text-dark" id="product-price">৳ {{ number_format($product->base_price, 2) }}</span>
                                <span class="h6 text-muted text-decoration-line-through ms-2" id="product-base-price" style="display: none;"></span>
                            @endif
                            </div>

                            <!--- color --->
                             @if($product->variants->isNotEmpty() && $product->variants->first()->color)
                        <div class="mb-4">
                            <h2 class="h5 fw-semibold mb-2">Color: <span id="selected-color-name">{{ $product->variants->first()->color->name }}</span></h2>
                            <div class="d-flex gap-2">
                                @foreach($product->variants as $variant)
                                    <div class="color-option {{ $loop->first ? 'active' : '' }}"
                                         style="background-color: {{ $variant->color->code }};"
                                         data-variant-id="{{ $variant->id }}"
                                         data-variant-sku="{{ $variant->variant_sku }}"
                                         data-color-name="{{ $variant->color->name }}"
                                         data-additional-price="{{ $variant->additional_price }}"
                                         data-sizes="{{ json_encode($variant->detailed_sizes) }}"
                                         data-main-images="{{ json_encode(array_merge($product->main_image ?? [], $variant->main_image ?? [])) }}"
                                         data-thumb-images="{{ json_encode(array_merge($product->thumbnail_image ?? [], $variant->variant_image ?? [])) }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                            <!--end color--->

                            <!-- size Chart-->

                             @if($product->assignChart && $product->assignChart->entries->isNotEmpty())
                        <div class="mb-4">
                            <h2 class="h5 fw-semibold mb-2">Size Chart:</h2>
                            <table class="table table-bordered table-sm text-center">
                                <thead>
                                    <tr class="bg-light">
                                        @foreach($product->assignChart->entries->first()->toArray() as $key => $value)
                                            @if(!in_array($key, ['id', 'assign_chart_id', 'created_at', 'updated_at']))
                                                <th class="p-2 text-uppercase">{{ str_replace('_', ' ', $key) }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->assignChart->entries as $entry)
                                    <tr>
                                        @foreach($entry->toArray() as $key => $value)
                                            @if(!in_array($key, ['id', 'assign_chart_id', 'created_at', 'updated_at']))
                                                <td class="p-2">{{ $value }}"</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif

                            <!-- Size Selection -->
                             <div class="mb-4">
                            <p class="mb-2">Size: <span id="selected-size-name" class="fw-semibold text-dark">Select a size</span></p>
                            <div id="size-options-container" class="d-flex gap-2">
                                {{-- Size buttons will be dynamically inserted here by JavaScript --}}
                            </div>
                        </div>

                            <!-- Quantity and Buttons -->
                           <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4">
                            <div class="d-flex align-items-center border rounded-3 overflow-hidden">
                                <button class="btn btn-light rounded-0" id="quantity-minus">-</button>
                                <span class="px-3" id="quantity-value">1</span>
                                <button class="btn btn-light rounded-0" id="quantity-plus">+</button>
                            </div>
                            <button class="btn btn-dark fw-semibold rounded-3 flex-grow-1 add-to-cart-button" id="add-to-cart">Add To Cart</button>
                            <button class="btn btn-secondary fw-semibold rounded-3 flex-grow-1 buy-button">Buy Now</button>
                        </div>

                            <!-- Actions and Share with Bootstrap Icons -->
                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between mt-4">
                                <div class="d-flex align-items-center space-x-4 mb-4 mb-sm-0">
                                    <a href="#" class="d-flex align-items-center text-secondary text-decoration-none">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        <span>Add to compare</span>
                                    </a>
                                    <a href="#"
                                        class="d-flex align-items-center text-secondary text-decoration-none ms-3">
                                        <i class="bi bi-heart me-1"></i>
                                        <span>Add to wishlist</span>
                                    </a>
                                </div>
                                <div class="d-flex align-items-center text-secondary">
                                    <span>Share:</span>
                                    <a href="#" class="ms-2 text-decoration-none text-secondary"><i
                                            class="bi bi-facebook fs-5"></i></a>
                                    <a href="#" class="ms-2 text-decoration-none text-secondary"><i
                                            class="bi bi-instagram fs-5"></i></a>
                                    <a href="#" class="ms-2 text-decoration-none text-secondary"><i
                                            class="bi bi-linkedin fs-5"></i></a>
                                </div>
                            </div>

                            <!-- Section: People Watching and Delivery/Payment -->
                            <div class="mt-4 pt-4 border-top">
                                <!-- People Watching -->
                                <div class="d-flex align-items-center bg-light p-3 rounded-3 mb-3">
                                    <i class="bi bi-eye text-muted me-2"></i>
                                    <span class="small text-muted">18 People watching this product now!</span>
                                </div>

                                <!-- Delivery Information -->
                                <div class="bg-white border p-3 rounded-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-truck me-2"></i>
                                            <span class="small">Regular Product Delivery</span>
                                        </div>
                                        <div class="d-flex align-items-center small text-muted">
                                            <span>2-3 Days</span>
                                            <span class="fw-semibold text-dark ms-3">Inside Dhaka BDT 70</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-truck me-2"></i>
                                            <span class="small">Customized Product Delivery</span>
                                        </div>
                                        <div class="d-flex align-items-center small text-muted">
                                            <span>4-6 Days</span>
                                            <span class="fw-semibold text-dark ms-3">Outside Dhaka BDT 130</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Methods -->
                                <div>
                                    <h3 class="small fw-semibold mb-2">Payment Methods:</h3>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://placehold.co/40x20/F5F5F5/4B5563?text=Bank" alt="Bank"
                                            class="img-fluid" style="height: 20px;">
                                        <img src="https://placehold.co/40x20/F5F5F5/4B5563?text=Bikash" alt="Bikash"
                                            class="img-fluid" style="height: 20px;">
                                        <img src="https://placehold.co/40x20/F5F5F5/4B5563?text=Visa" alt="Visa"
                                            class="img-fluid" style="height: 20px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <!-- New Section: Description and Customer Reviews -->
                    <h2 class="h5 fw-semibold mb-3">Description</h2>
                    <div class="bg-white p-4 rounded-3 border">
                       {!! $product->description !!}
                    </div>

                    <h2 class="h5 fw-semibold mt-5 mb-3">Customer Reviews</h2>
                    <div class="row g-4">
                        <!-- Left Column: Review Summary -->
                        <div class="col-12 col-lg-6 p-4 rounded-3 border bg-white">
                            <div class="text-center mb-4">
                                <div class="star-rating fs-3 mb-2">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <p class="text-muted">0 reviews</p>
                                <p class="mt-4 small fw-semibold">Overall Rating: 0.0/5.0</p>
                            </div>
                            <div class="space-y-2">
                                <!-- 5 Star Bar -->
                                <div class="d-flex align-items-center">
                                    <span class="small">5 Star</span>
                                    <div class="review-bar-container mx-2">
                                        <div class="review-bar-fill" style="width: 0%;"></div>
                                    </div>
                                    <span class="small text-muted">0</span>
                                </div>
                                <!-- 4 Star Bar -->
                                <div class="d-flex align-items-center">
                                    <span class="small">4 Star</span>
                                    <div class="review-bar-container mx-2">
                                        <div class="review-bar-fill" style="width: 0%;"></div>
                                    </div>
                                    <span class="small text-muted">0</span>
                                </div>
                                <!-- 3 Star Bar -->
                                <div class="d-flex align-items-center">
                                    <span class="small">3 Star</span>
                                    <div class="review-bar-container mx-2">
                                        <div class="review-bar-fill" style="width: 0%;"></div>
                                    </div>
                                    <span class="small text-muted">0</span>
                                </div>
                                <!-- 2 Star Bar -->
                                <div class="d-flex align-items-center">
                                    <span class="small">2 Star</span>
                                    <div class="review-bar-container mx-2">
                                        <div class="review-bar-fill" style="width: 0%;"></div>
                                    </div>
                                    <span class="small text-muted">0</span>
                                </div>
                                <!-- 1 Star Bar -->
                                <div class="d-flex align-items-center">
                                    <span class="small">1 Star</span>
                                    <div class="review-bar-container mx-2">
                                        <div class="review-bar-fill" style="width: 0%;"></div>
                                    </div>
                                    <span class="small text-muted">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Review Form -->
                        <div class="col-12 col-lg-6 p-4 rounded-3 border bg-white">
                            <h3 class="h6 fw-semibold mb-2">Be the first to review “Gojo Satoru- premium acid wash”</h3>
                            <p class="small text-muted mb-4">Your email address will not be published. Required fields
                                are marked *</p>

                            <div class="mb-3">
                                <label class="small">Your rating *:</label>
                                <div class="star-rating fs-5 text-muted space-x-1 cursor-pointer">
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Your review *</label>
                                <textarea class="form-control" rows="3" placeholder="Your review *"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Pros</label>
                                <input type="text" class="form-control" placeholder="Pros">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Cons</label>
                                <input type="text" class="form-control" placeholder="Cons">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Name *</label>
                                <input type="text" class="form-control" placeholder="Name *">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small">Email *</label>
                                <input type="email" class="form-control" placeholder="Email *">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="save-info">
                                <label class="form-check-label small" for="save-info">Save my name, email, and website
                                    in this browser for the next time I comment.</label>
                            </div>

                            <button class="btn btn-dark fw-semibold rounded-3 w-100">Submit</button>
                        </div>
                    </div>
            </div>

        </section>
    </main>
@endsection
@section('script')
<script>
$(document).ready(function() {
    // --- Configuration ---
    const BASE_PRODUCT_PRICE = {{ $product->discount_price ?? $product->base_price }};
    const IMAGE_BASE_URL = "{{ $front_ins_url . 'public/uploads/' }}";
    let selectedVariantId = null;
    let selectedSize = null;
    
    // --- Slick Slider Initialization ---
    function initializeSlick() {
        // Destroy existing sliders if they exist
        if ($('#main-product-slider').hasClass('slick-initialized')) {
            $('#main-product-slider').slick('unslick');
        }
        if ($('#thumbnail-nav-slider').hasClass('slick-initialized')) {
            $('#thumbnail-nav-slider').slick('unslick');
        }

        $('#main-product-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '#thumbnail-nav-slider'
        });

        $('#thumbnail-nav-slider').slick({
            slidesToShow: 3,
            slidesToScroll: 1,
            vertical: true,
            asNavFor: '#main-product-slider',
            dots: false,
            arrows: false,
            focusOnSelect: true,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: 4,
                    vertical: false
                }
            }]
        });
    }

    // Custom arrow functionality
    $('.prev-arrow').click(() => $('#thumbnail-nav-slider').slick('slickPrev'));
    $('.next-arrow').click(() => $('#thumbnail-nav-slider').slick('slickNext'));

    // --- Core Logic Functions ---
    function updateSizes(sizes) {
        const container = $('#size-options-container');
        container.empty(); // Clear old sizes
        $('#selected-size-name').text('Select a size');
        selectedSize = null;

        if (sizes && sizes.length > 0) {
            sizes.forEach(size => {
                const button = $('<button></button>')
                    .addClass('btn btn-outline-secondary rounded-3 size-option')
                    .text(size.name)
                    .data('size-name', size.name);
                
                if (size.quantity <= 0) {
                    button.prop('disabled', true);
                }
                container.append(button);
            });
        } else {
            container.html('<p class="text-danger small">This color is currently out of stock.</p>');
        }
    }

    function updateImages(mainImages, thumbImages) {
        const mainSlider = $('#main-product-slider');
        const thumbSlider = $('#thumbnail-nav-slider');
        
        mainSlider.empty();
        thumbSlider.empty();

        mainImages.forEach(img => {
            mainSlider.append(`<div><img src="${IMAGE_BASE_URL}${img}" class="img-fluid rounded-3"></div>`);
        });

        thumbImages.forEach(img => {
            thumbSlider.append(`<div><img src="${IMAGE_BASE_URL}${img}" class="img-fluid rounded-3 thumbnail-image"></div>`);
        });

        initializeSlick();
    }
    
    function updatePrice(additionalPrice) {
        const finalPrice = BASE_PRODUCT_PRICE + parseFloat(additionalPrice || 0);
        $('#product-price').text(`৳ ${finalPrice.toFixed(2)}`);
    }

    // --- Event Handlers ---
    $('.color-option').on('click', function() {
        const $this = $(this);

        // Update active state
        $('.color-option').removeClass('active');
        $this.addClass('active');

        // Extract data
        const variantData = $this.data();
        selectedVariantId = variantData.variantId;
        
        // Update UI
        $('#selected-color-name').text(variantData.colorName);
        $('#product-sku').text(variantData.variantSku || '{{ $product->product_code }}');
        updateSizes(variantData.sizes);
        updatePrice(variantData.additionalPrice);
        updateImages(variantData.mainImages, variantData.thumbImages);
    });

    $('#size-options-container').on('click', '.size-option:not(:disabled)', function() {
        const $this = $(this);
        $('#size-options-container .size-option').removeClass('active');
        $this.addClass('active');
        selectedSize = $this.data('size-name');
        $('#selected-size-name').text(selectedSize);
    });

    $('#quantity-plus').on('click', () => {
        let qty = parseInt($('#quantity-value').text());
        $('#quantity-value').text(++qty);
    });

    $('#quantity-minus').on('click', () => {
        let qty = parseInt($('#quantity-value').text());
        if (qty > 1) {
            $('#quantity-value').text(--qty);
        }
    });

    $('#add-to-cart').on('click', function() {
        // Validation
        if (!selectedVariantId) {
            alert('Please select a color.');
            return;
        }
        if (!selectedSize) {
            alert('Please select a size.');
            return;
        }
        
        const $button = $(this);
        const cartData = {
            productId: {{ $product->id }},
            variantId: selectedVariantId,
            size: selectedSize,
            quantity: parseInt($('#quantity-value').text()),
            _token: "{{ csrf_token() }}" 
        };

        // AJAX call to add the product to the cart
        $.ajax({
            url: '{{ route("cart.add") }}',
            type: 'POST',
            data: cartData,
            beforeSend: function() {
                // Provide visual feedback
                $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
            },
            success: function(response) {
                if (response.success) {
                    // Update the cart display everywhere
                    updateCartOffcanvas();
                    
                    // Automatically open the cart offcanvas to show the user their new item
                    const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
                    cartOffcanvas.show();
                } else {
                    alert(response.message || 'An unknown error occurred.');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Restore the button to its original state
                $button.prop('disabled', false).html('Add To Cart');
            }
        });
    });

    // --- Initial Page Load ---
    initializeSlick();
    // Trigger a click on the first color to initialize sizes and prices
    $('.color-option.active').first().trigger('click');
});
</script>
@endsection