@extends('front.master.master')

@section('title')
{{ $bundleDeal->title ?? 'Bundle Offer' }}
@endsection
@section('css')
@endsection
@section('body')
<section class="section">
            <div class="container">
                <div class="spotlight_combo_page_product_container container my-5 p-4 bg-white rounded-4 shadow">

                    <!-- Product Page Header -->
                    <div class="d-flex align-items-center text-muted small mb-4">
                <span><a href="{{ route('home.index') }}" class="text-muted text-decoration-none">Home</a></span>
                <span class="mx-2">/</span>
                <span><a href="{{ route('offer.show', $bundleDeal->bundleOffer->name) }}" class="text-muted text-decoration-none">{{ $bundleDeal->bundleOffer->name }}</a></span>
                <span class="mx-2">/</span>
                <span class="fw-semibold text-dark">{{ $bundleDeal->title }}</span>
            </div>

                    <div class="row g-4">
                        <!-- Left Side: Image Gallery -->
                        <div class="col-12 col-lg-6 d-flex">
                            <div class="d-flex flex-column align-items-center">
                                <!-- Thumbnail slider for navigation -->
                                <div id="thumbnail-nav-slider" class="w-100 spotlight_combo_page_thumbnail_nav"
                                    style="max-width: 100px;">
                                      @forelse ($allImages as $image)
                                <div>
                                    <img src="{{ $front_ins_url .'public/uploads/' . $image }}"
                                         alt="{{ $bundleDeal->title }} thumbnail"
                                         class="img-fluid rounded-3 spotlight_combo_page_thumbnail_image">
                                </div>
                            @empty
                                <div>
                                    <img src="https://placehold.co/100x100/F5F5F5/4B5563?text=No+Image"
                                         alt="No Image available"
                                         class="img-fluid rounded-3 spotlight_combo_page_thumbnail_image">
                                </div>
                            @endforelse
                                </div>
                                <!-- Custom arrow controls placed below the thumbnail column -->
                                 @if (count($allImages) > 3)
                        <div class="spotlight_combo_page_custom_arrows d-flex justify-content-start align-items-center w-100 mt-2">
                            <button class="prev-arrow"><i class="bi bi-chevron-up"></i></button>
                            <button class="next-arrow"><i class="bi bi-chevron-down"></i></button>
                        </div>
                        @endif
                            </div>

                            <!-- Main image slider -->
                            <div id="main-product-slider" class="flex-grow-1 rounded-3 overflow-hidden ms-4">
                                 @forelse ($allImages as $image)
                            <div>
                                <img src="{{ $front_ins_url .'public/uploads/' . $image }}"
                                     alt="{{ $bundleDeal->title }} main image" class="img-fluid rounded-3">
                            </div>
                        @empty
                            <div>
                                <img src="https://placehold.co/1000x1000/F5F5F5/4B5563?text=No+Image+Available"
                                     alt="No Image available" class="img-fluid rounded-3">
                            </div>
                        @endforelse
                            </div>
                        </div>

                        <!-- Right Side: Product Details -->
                        <div class="col-12 col-lg-6 d-flex flex-column p-4 spotlight_combo_page_product_details">
                             <h1 class="h3 fw-semibold text-dark mb-2">{{ $bundleDeal->title }}</h1>
                    <p class="h6 text-muted mb-4">SKU: BDL-{{ $bundleDeal->id }}</p>
                    <div class="d-flex align-items-baseline mb-4">
                         @if($bundleDeal->discount_price > 0 && $bundleDeal->discount_price < $totalBasePrice)
                            <del class="text-muted h4 me-2">৳{{ number_format($totalBasePrice) }}</del>
                            <span class="h3 fw-bold text-dark">৳{{ number_format($bundleDeal->discount_price) }}</span>
                        @else
                            <span class="h3 fw-bold text-dark">৳{{ number_format($totalBasePrice) }}</span>
                        @endif
                    </div>
                            <!-- Product Selection Boxes -->
<div class="row g-2 mb-4">
    @for ($i = 0; $i < $bundleDeal->buy_quantity; $i++)
    <div class="col-6">
        {{-- This button needs a unique ID and a data attribute to track its slot --}}
        <button
            id="select-btn-slot-{{ $i }}"
            class="btn btn-outline-secondary w-100 p-2 spotlight_combo_page_select-button"
            data-bs-toggle="modal"
            data-bs-target="#productSelectModal"
            data-slot-index="{{ $i }}">
            {{-- This inner div is what gets replaced by the script --}}
            <div class="d-flex flex-column align-items-center justify-content-center py-3">
                <i class="bi bi-plus-lg fs-4 mb-2"></i>
                <span class="small">Please select product {{ $i + 1 }}</span>
            </div>
        </button>
    </div>
    @endfor
</div>
                            <div id="selected-products-list-container" class="vstack gap-2 mb-4">
    @for ($i = 0; $i < $bundleDeal->buy_quantity; $i++)
        <div id="selected-item-slot-{{ $i }}" class="d-flex justify-content-between align-items-center py-2 ">
            
            {{-- The <a> tag now wraps both the text and the icon --}}
            <a href="#" class="text-muted text-decoration-none change-product-btn d-flex align-items-center" 
               data-bs-toggle="modal" 
               data-bs-target="#productSelectModal" 
               data-slot-index="{{ $i }}">
                <span class="me-2">Please select your product!</span>
                <i class="bi bi-pencil-square"></i>
            </a>

            {{-- Right side: Price placeholder --}}
            <div class="text-end text-muted">
                <span class="me-2">------</span>
                <span>------</span>
            </div>

        </div>
    @endfor
</div>

                            <hr class="my-4">

                            <!-- Combo Price Section -->
                            <div class="d-flex align-items-center mb-4">
                                <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                                <span class="fw-semibold">Please select a product for all items.</span>
                            </div>

                            <!-- Quantity and Buttons -->
                            <div class="d-flex flex-column flex-sm-row align-items-center gap-4 mb-4">
                                <div class="d-flex align-items-center border rounded-3 overflow-hidden quantity-selector">
    <button class="btn btn-light rounded-0 quantity-decrease">-</button>
    <span class="px-3 quantity-input">1</span>
    <button class="btn btn-light rounded-0 quantity-increase">+</button>
</div>
                                <button class="btn btn-dark fw-semibold rounded-3 flex-grow-1 add-to-cart-button">Add To
                                    Cart</button>
                                <button class="btn btn-secondary fw-semibold rounded-3 flex-grow-1 buy-button">Buy
                                    Now</button>
                            </div>

                            <!-- Actions and Share with Bootstrap Icons -->
                            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between mt-4">
                                <div class="d-flex align-items-center space-x-4 mb-4 mb-sm-0">
                                    <a href="#" class="d-flex align-items-center text-secondary text-decoration-none">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        <span>Add to compare</span>
                                    </a>
                                    <a href="#"  id="add-bundle-to-wishlist"
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
                    <h3 class="fw-semibold mb-2">Product Details:</h3>
                    <ul class="list-unstyled space-y-1 text-sm text-secondary">
                        <li class="d-flex align-items-center"><i class="bi bi-apparel-fill me-2 text-muted"></i>
                            Fabric: Premium 100% Cotton Combed Yarn</li>
                        <li class="d-flex align-items-center"><i class="bi bi-speedometer me-2 text-muted"></i>
                            Weight: 240+ GSM (Thicker. Stronger. Better.)</li>
                        <li class="d-flex align-items-center"><i class="bi bi-tshirt-fill me-2 text-muted"></i> Fit:
                            Relaxed Oversized Drop Shoulder</li>
                        <li class="d-flex align-items-center"><i class="bi bi-images me-2 text-muted"></i> Print:
                            Ultra-detailed, fade-resistant anime graphics</li>
                        <li class="d-flex align-items-center"><i class="bi bi-palette-fill me-2 text-muted"></i>
                            Finish: Unique Acid Wash</li>
                        <li class="d-flex align-items-center"><i class="bi bi-rulers me-2 text-muted"></i> Sizes: M,
                            L, XL, XXL</li>
                    </ul>
                    <h3 class="fw-semibold mt-4 mb-2">Why you'll love it:</h3>
                    <ul class="list-unstyled space-y-1 text-sm text-secondary">
                        <li class="d-flex align-items-center"><i class="bi bi-check-lg me-2 text-success"></i>
                            Shadow Army visuals that command attention</li>
                        <li class="d-flex align-items-center"><i class="bi bi-check-lg me-2 text-success"></i>
                            Heavyweight yet breathable fabric for all-day comfort</li>
                        <li class="d-flex align-items-center"><i class="bi bi-check-lg me-2 text-success"></i>
                            Pre-shrunk for a consistent fit</li>
                        <li class="d-flex align-items-center"><i class="bi bi-check-lg me-2 text-success"></i>
                            One-of-a-kind finish—no two tees are exactly the same</li>
                    </ul>
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
        <!-- Modal for Product Selection -->
    <div class="modal fade" id="productSelectModal" tabindex="-1" aria-labelledby="productSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content spotlight_combo_page_modal-card">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn btn-sm btn-link me-2" id="modal-back-btn" style="display: none;">
                    <i class="bi bi-arrow-left"></i> Back
                </button>
                <h5 class="modal-title fw-semibold" id="productSelectModalLabel">PLEASE SELECT YOUR PRODUCT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-product-view">
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        @forelse ($productsCollection as $product)
                        <div class="col">
                            <div class="card h-100 initial-product-option" style="cursor: pointer;" data-product-id="{{ $product->id }}">
                                <img src="{{ (is_array($product->main_image) && count($product->main_image) > 0) ? $front_ins_url .'public/uploads/' . $product->main_image[0] : 'https://placehold.co/300x300' }}" 
                                     alt="{{ $product->name }}" class="card-img-top">
                                <div class="card-body text-center p-2">
                                    <h6 class="card-title small">{{ $product->name }}</h6>
                                    {{-- START: ADDED PRICE DISPLAY --}}
                    @php
                        // Calculate the discounted per-item price for the bundle
                        $discountedPricePerItem = $bundleDeal->discount_price / $bundleDeal->buy_quantity;
                    @endphp
                    <p class="card-text small">
                        <del class="text-muted me-1">৳{{ number_format($product->base_price, 1) }}</del>
                        <span class="fw-bold">৳{{ number_format($discountedPricePerItem, 1) }}</span>
                    </p>
                    {{-- END: ADDED PRICE DISPLAY --}}
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12"><p class="text-center">No products found.</p></div>
                        @endforelse
                    </div>
                </div>

                <div id="modal-variant-view" style="display: none;">
                    </div>
            </div>
        </div>
    </div>
</div>


@endsection
<script id="products-with-variants-data" type="application/json">
    @json($productsCollection->keyBy('id'))
</script>
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
    $(document).ready(function(){
        // 1. Initialize the Slick sliders for the main product image gallery
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
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        vertical: false,
                        slidesToShow: 4
                    }
                }
            ]
        });
        
        $('.prev-arrow').click(function(){
            $('#thumbnail-nav-slider').slick('slickPrev');
        });

        $('.next-arrow').click(function(){
            $('#thumbnail-nav-slider').slick('slickNext');
        });

                // --- START: SCRIPT FOR TWO-STEP BUNDLE SELECTION MODAL ---

        const productsData = JSON.parse(document.getElementById('products-with-variants-data').textContent);
        const modalEl = document.getElementById('productSelectModal');
        const modal = new bootstrap.Modal(modalEl);
        const modalTitle = $('#productSelectModalLabel');
        const productView = $('#modal-product-view');
        const variantView = $('#modal-variant-view');
        const backBtn = $('#modal-back-btn');
       


        let selectedProducts = {};
        let currentSlotIndex = null;
        const initialModalTitle = modalTitle.text();

        $(document).on('click', '.spotlight_combo_page_select-button, .change-product-btn', function() {
            currentSlotIndex = $(this).data('slot-index');
        });

        $(document).on('click', '.initial-product-option', function() {
            const productId = $(this).data('productId');
            const product = productsData[productId];
            let variantHTML = '<div class="row row-cols-2 row-cols-md-3 g-3">';
            product.variants.forEach(variant => {
                variant.detailed_sizes.forEach(sizeInfo => {
                    const imagePath = (variant.variant_image && variant.variant_image.length > 0)
                        ? variant.variant_image[0]
                        : (product.main_image && product.main_image.length > 0 ? product.main_image[0] : null);
                    const variantImage = imagePath ? `{{ $front_ins_url . 'public/uploads/' }}${imagePath}` : 'https://placehold.co/300x300';

                    const basePrice = parseFloat(product.base_price) + parseFloat(variant.additional_price || 0);
                    const discountedPrice = {{ $bundleDeal->discount_price / $bundleDeal->buy_quantity }};

                    variantHTML += `
                        <div class="col">
                            <div class="card h-100 final-variant-option" style="cursor: pointer;"
                                 data-product-id="${product.id}" data-variant-id="${variant.id}" data-size-name="${sizeInfo.name}"
                                 data-product-name="${product.name}" data-color-name="${variant.color ? variant.color.name : ''}"
                                 data-base-price="${basePrice}" data-final-price="${discountedPrice}" data-product-image="${variantImage}">
                                <img src="${variantImage}" class="card-img-top" alt="${product.name}">
                                <div class="card-body text-center p-2">
                                    <h6 class="card-title small mb-1">${product.name}</h6>
                                    <p class="card-text small mb-1">Size: ${sizeInfo.name}</p>
                                    <p class="card-text small">
                                        <del class="text-muted me-1">৳${basePrice.toFixed(1)}</del>
                                        <span class="fw-bold">৳${discountedPrice.toFixed(1)}</span>
                                    </p>
                                </div>
                            </div>
                        </div>`;
                });
            });
            variantHTML += '</div>';
            modalTitle.text(product.name);
            variantView.html(variantHTML).show();
            productView.hide();
            backBtn.show();
        });

        $(document).on('click', '.final-variant-option', function() {
            const productData = {
                id: $(this).data('productId'),
                variantId: $(this).data('variantId'),
                name: $(this).data('productName'),
                basePrice: parseFloat($(this).data('basePrice')),
                finalPrice: parseFloat($(this).data('finalPrice')),
                image: $(this).data('productImage'),
                size: $(this).data('sizeName'),
                color: $(this).data('colorName')
            };

            selectedProducts[currentSlotIndex] = productData;
            // THIS IS THE KEY FUNCTION CALL
            updateSelectionUI(currentSlotIndex, productData);
            updateSelectedItemSlot(currentSlotIndex, productData);
            modal.hide();
        });

        backBtn.on('click', function() {
            modalTitle.text(initialModalTitle);
            productView.show();
            variantView.hide();
            backBtn.hide();
        });

        modalEl.addEventListener('hidden.bs.modal', function (event) {
            modalTitle.text(initialModalTitle);
            productView.show();
            variantView.hide().html('');
            backBtn.hide();
        });

        /**
         * THIS IS THE FUNCTION THAT UPDATES THE BUTTON'S APPEARANCE
         */
        function updateSelectionUI(slotIndex, product) {
            const button = $(`#select-btn-slot-${slotIndex}`);
            const buttonContent = `
                <div class="d-flex align-items-center p-1 text-start">
                    <img src="${product.image}" alt="${product.name}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-2">
                    <div>
                        <span class="small d-block fw-bold">${product.name}</span>
                        <span class="small d-block text-muted">Size: ${product.size}</span>
                    </div>
                </div>
            `;
            button.removeClass('btn-outline-secondary').addClass('btn-light');
            button.html(buttonContent);
        }

        /**
         * This function updates the list below the buttons.
         */
        function updateSelectedItemSlot(slotIndex, product) {
            const slotElement = $(`#selected-item-slot-${slotIndex}`);
            if (slotElement.length === 0) return;
            const updatedHTML = `
                <a href="#" class="text-muted text-decoration-none change-product-btn d-flex align-items-center"
                   data-bs-toggle="modal" data-bs-target="#productSelectModal" data-slot-index="${slotIndex}">
                    <span class="me-2">${product.name} Size: ${product.size}</span>
                    <i class="bi bi-pencil-square"></i>
                </a>
                <div class="text-end">
                    <del class="text-muted me-2">৳${product.basePrice.toFixed(1)}</del>
                    <span class="fw-bold">৳${product.finalPrice.toFixed(1)}</span>
                </div>
            `;
            const parentDiv = slotElement.empty().addClass('d-flex justify-content-between align-items-center py-2 border-bottom');
            parentDiv.html(updatedHTML);
        }

        // --- START: Quantity Selector Logic ---
        $('.quantity-decrease').on('click', function() {
            let quantityInput = $(this).siblings('.quantity-input');
            let currentQuantity = parseInt(quantityInput.text());
            if (currentQuantity > 1) {
                quantityInput.text(currentQuantity - 1);
            }
        });

        $('.quantity-increase').on('click', function() {
            let quantityInput = $(this).siblings('.quantity-input');
            let currentQuantity = parseInt(quantityInput.text());
            quantityInput.text(currentQuantity + 1);
        });
        // --- END: Quantity Selector Logic ---


        // --- START: SCRIPT FOR ADDING BUNDLE TO CART ---
        $('.add-to-cart-button').on('click', function() {
            const buyQuantity = {{ $bundleDeal->buy_quantity }};
            const selectedCount = Object.keys(selectedProducts).length;

            // 1. Validate: Check if all slots are filled
            if (selectedCount < buyQuantity) {
               Swal.fire({
                  icon: 'warning',
                  title: 'Incomplete Selection',
                  text: 'Please select a product for all available slots before adding to cart.'
                });
                return; // Stop the function
            }
            
            const button = $(this);
            button.prop('disabled', true).text('Adding...');

            // 2. Prepare Payload
            const selectedProductsArray = Object.values(selectedProducts);
            const payload = {
                _token: '{{ csrf_token() }}',
                bundleId: {{ $bundleDeal->id }},
                quantity: parseInt($('.quantity-input').text()),
                selectedProducts: selectedProductsArray
            };

            // 3. AJAX Request
            $.ajax({
                url: '{{ route("cart.addBundle") }}',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
    // Show a success notification toast
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: response.message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // Call a function to update the mini-cart display
    // Make sure this function is defined in your global JS file
    updateCartOffcanvas(); 
    
} else {
    // Show an error popup
    Swal.fire({
        icon: 'error',
        title: 'Request Failed',
        text: 'An unknown error occurred. Please try again.'
    });
}
                },
                error: function(xhr) {
                    let errorMessage = 'Could not add bundle to cart. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: errorMessage
                    });
                },
                complete: function() {
                     button.prop('disabled', false).text('Add To Cart');
                }
            });
        });
        // --- END: SCRIPT FOR ADDING BUNDLE TO CART ---


         // --- START: SCRIPT FOR ADDING BUNDLE TO CART ---
        $('.buy-button').on('click', function() {
            const buyQuantity = {{ $bundleDeal->buy_quantity }};
            const selectedCount = Object.keys(selectedProducts).length;

            // 1. Validate: Check if all slots are filled
            if (selectedCount < buyQuantity) {
               Swal.fire({
                  icon: 'warning',
                  title: 'Incomplete Selection',
                  text: 'Please select a product for all available slots before adding to cart.'
                });
                return; // Stop the function
            }
            
            const button = $(this);
            button.prop('disabled', true).text('Adding...');

            // 2. Prepare Payload
            const selectedProductsArray = Object.values(selectedProducts);
            const payload = {
                _token: '{{ csrf_token() }}',
                bundleId: {{ $bundleDeal->id }},
                quantity: parseInt($('.quantity-input').text()),
                selectedProducts: selectedProductsArray
            };

            // 3. AJAX Request
            $.ajax({
                url: '{{ route("cart.addBundle") }}',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
    // Show a success notification toast
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: response.message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // Call a function to update the mini-cart display
    // Make sure this function is defined in your global JS file
    updateCartOffcanvas(); 

     @auth
                        // If user is logged in, redirect straight to checkout
                        window.location.href = "{{ route('user.checkout') }}";
                    @else
                        // If user is a guest, open the login/register modal
                        const signInModal = new bootstrap.Modal(document.getElementById('signInOffcanvas'));
                        signInModal.show();
                    @endauth
    
} else {
    // Show an error popup
    Swal.fire({
        icon: 'error',
        title: 'Request Failed',
        text: 'An unknown error occurred. Please try again.'
    });
}
                },
                error: function(xhr) {
                    let errorMessage = 'Could not add bundle to cart. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: errorMessage
                    });
                },
                complete: function() {
                     button.prop('disabled', false).text('Add To Cart');
                }
            });
        });
        // --- END: SCRIPT FOR ADDING BUNDLE TO CART ---

        // --- NEW SCRIPT FOR ADDING BUNDLE TO WISHLIST ---
    $('#add-bundle-to-wishlist').on('click', function() {
        @auth
            // --- USER IS LOGGED IN ---
            const buyQuantity = {{ $bundleDeal->buy_quantity }};
            const selectedCount = Object.keys(selectedProducts).length;

            if (selectedCount < buyQuantity) {
                Swal.fire({ icon: 'warning', title: 'Incomplete Selection', text: 'Please select a product for all available slots first.' });
                return;
            }

            const $button = $(this);
            const payload = {
                selected_products: Object.values(selectedProducts),
                _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: '{{ route("wishlist.addBundle") }}',
                type: 'POST',
                data: payload,
                beforeSend: function() {
                    $button.prop('disabled', true).find('span').text('Adding...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: xhr.responseJSON.message || 'Something went wrong.' });
                },
                complete: function() {
                    $button.prop('disabled', false).find('span').text('Add to wishlist');
                }
            });

        @else
            // --- USER IS A GUEST ---
            Swal.fire({
                title: 'Login Required',
                text: "You need to be logged in to add items to your wishlist.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Login or Register',
                cancelButtonText: 'Not Now'
            }).then((result) => {
                 if (result.isConfirmed) {
                    // --- START OF NEW, MORE ROBUST FIX ---
                    const quickViewModalEl = document.getElementById('quickViewModal');
                    const quickViewModalInstance = bootstrap.Modal.getInstance(quickViewModalEl);
                    const signInOffcanvas = new bootstrap.Offcanvas(document.getElementById('signInOffcanvas'));

                    // 1. Hide the quick view modal
                    if (quickViewModalInstance) {
                        quickViewModalInstance.hide();
                    }

                    // 2. Manually remove the backdrop and cleanup body styles.
                    //    This forcefully resets the state and prevents conflicts.
                    $('.modal-backdrop').remove();
                    $('body').removeAttr('style').removeClass('modal-open');
                    
                    // 3. Show the sign-in offcanvas.
                    signInOffcanvas.show();
                    // --- END OF NEW FIX ---
                }
            });
        @endauth
    });

    });

</script>
@endsection