

@extends('front.master.master')

@section('title')
Your Cart
@endsection

@section('css')
{{-- You can add specific CSS here if needed --}}
@endsection

@section('body')
 <section class="section">
            <div class="container">
                <!-- Header with Progress Bar -->
                <header class="spotlight_cart_progress_header">
                    <div class="container">
                        <nav class="d-flex justify-content-center align-items-center fw-semibold">
                            <span class="spotlight_cart_progress_item">SHOPPING CART</span>
                            <span class="spotlight_cart_progress_item opacity-50">CHECKOUT</span>
                            <span class="spotlight_cart_progress_item opacity-50">ORDER COMPLETE</span>
                        </nav>
                    </div>
                </header>

                <div class="container spotlight_cart_container">
                    <!-- Coupon and Free Shipping Alert -->
                    <div class="alert alert-success d-flex align-items-center small py-2 rounded-pill" role="alert">
                        <i class="bi bi-cart3 me-2"></i>
                        <span>Add **৳ 1,901.0** to cart and get free shipping!</span>
                    </div>

                    <div class="row g-4">
                        <!-- Left Side: Cart Items -->
                        <div class="col-12 col-lg-8">
                            <div class="spotlight_cart_summary_card">
                                <h5 class="fw-semibold">Product</h5>
                                <div class="table-responsive">
                                    <table class="spotlight_cart_table">
                                        <thead>
                                            <tr>
                                                <th class="text-start">PRODUCT</th>
                                                <th>PRICE</th>
                                                <th>QUANTITY</th>
                                                <th>SUBTOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Cart Item 1 -->
                                            <tr>
                                                <td class="d-flex align-items-center text-start" data-label="PRODUCT">
                                                    <button class="btn btn-sm btn-link text-muted me-2 p-0"><i
                                                            class="bi bi-x"></i></button>
                                                    <img src="https://placehold.co/100x100/F5F5F5/4B5563?text=Product+1"
                                                        alt="BERSERK ARMOUR" class="spotlight_cart_item_image me-3">
                                                    <div class="spotlight_cart_item_details">
                                                        <h6 class="mb-0 fw-semibold">BERSERK ARMOUR - Exclusive
                                                            DropShoulder</h6>
                                                        <p class="text-muted small mb-0">- M</p>
                                                    </div>
                                                </td>
                                                <td data-label="PRICE" class="fw-semibold">৳ 799.0</td>
                                                <td data-label="QUANTITY">
                                                    <div class="spotlight_cart_item_quantity mx-auto">
                                                        <button class="btn btn-light">-</button>
                                                        <span class="px-2">1</span>
                                                        <button class="btn btn-light">+</button>
                                                    </div>
                                                </td>
                                                <td data-label="SUBTOTAL" class="fw-semibold">৳ 799.0</td>
                                            </tr>
                                            <!-- Cart Item 2 (Main Bundle Product) -->
                                            <tr>
                                                <td class="d-flex align-items-center text-start" data-label="PRODUCT">
                                                    <button class="btn btn-sm btn-link text-muted me-2 p-0"><i
                                                            class="bi bi-x"></i></button>
                                                    <img src="https://placehold.co/100x100/F5F5F5/4B5563?text=Product+2"
                                                        alt="2 Drop Shoulder" class="spotlight_cart_item_image me-3">
                                                    <div class="spotlight_cart_item_details">
                                                        <h6 class="mb-0 fw-semibold">2 Drop Shoulder</h6>
                                                        <p class="text-muted small mb-0">Combo Bundle</p>
                                                    </div>
                                                </td>
                                                <td data-label="PRICE" class="fw-semibold">৳ 800.0</td>
                                                <td data-label="QUANTITY">
                                                    <div class="spotlight_cart_item_quantity mx-auto">
                                                        <button class="btn btn-light">-</button>
                                                        <span class="px-2">1</span>
                                                        <button class="btn btn-light">+</button>
                                                    </div>
                                                </td>
                                                <td data-label="SUBTOTAL" class="fw-semibold">৳ 800.0</td>
                                            </tr>
                                            <!-- Nested Product 3 -->
                                            <tr class="spotlight_cart_nested_item">
                                                <td data-label="PRODUCT" class="text-start">
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://placehold.co/50x50/F5F5F5/4B5563?text=Prod+3"
                                                            alt="PREMIUM DROP-SHOULDER"
                                                            class="spotlight_cart_nested_item_image me-2">
                                                        <div class="spotlight_cart_nested_item_details">
                                                            <h6 class="mb-0 text-muted small">PREMIUM DROP-SHOULDER - L
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td data-label="PRICE" class="fw-semibold text-center">৳ 380.0</td>
                                                <td data-label="QUANTITY" class="text-center">1</td>
                                                <td data-label="SUBTOTAL" class="fw-semibold text-center">৳ 380.0</td>
                                            </tr>
                                            <!-- Nested Product 4 -->
                                            <tr class="spotlight_cart_nested_item">
                                                <td data-label="PRODUCT" class="text-start">
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://placehold.co/50x50/F5F5F5/4B5563?text=Prod+4"
                                                            alt="PREMIUM DROP-SHOULDER"
                                                            class="spotlight_cart_nested_item_image me-2">
                                                        <div class="spotlight_cart_nested_item_details">
                                                            <h6 class="mb-0 text-muted small">PREMIUM DROP-SHOULDER - M
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td data-label="PRICE" class="fw-semibold text-center">৳ 380.0</td>
                                                <td data-label="QUANTITY" class="text-center">1</td>
                                                <td data-label="SUBTOTAL" class="fw-semibold text-center">৳ 380.0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex mt-4">
                                    <input type="text" class="form-control me-2" placeholder="Coupon code">
                                    <button class="btn btn-outline-secondary">Apply Coupon</button>
                                </div>
                            </div>

                            <!-- You May Be Interested Section -->
                            <div class="spotlight_cart_interest_card">
                                <h5 class="fw-semibold">You May Be Interested In...</h5>
                                <div class="row row-cols-2 row-cols-md-4 g-3 mt-3">
                                    <!-- Product 1 -->
                                    <div class="col">
                                        <div class="product-card card">
                                            <img src="assets/img/product/product.webp" class="card-img-top"
                                                alt="Product 6">
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
                                            <img src="assets/img/product/product.webp" class="card-img-top"
                                                alt="Product 6">
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
                                            <img src="assets/img/product/product.webp" class="card-img-top"
                                                alt="Product 6">
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
                                            <img src="assets/img/product/product.webp" class="card-img-top"
                                                alt="Product 6">
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

                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Cart Totals & Delivery -->
                        <div class="col-12 col-lg-4">
                            <div class="spotlight_cart_summary_card mb-4">
                                <h5 class="fw-semibold">Cart Totals</h5>
                                <div class="spotlight_cart_total_row border-bottom">
                                    <span class="fw-semibold">Subtotal</span>
                                    <span class="fw-semibold">৳ 2,359.0</span>
                                </div>
                                <div class="spotlight_cart_total_row mb-3">
                                    <span class="fw-semibold">Shipping</span>
                                    <div class="d-flex flex-column align-items-end small">
                                        <span>Inside Dhaka: ৳ 80.0</span>
                                        <span>Outside Dhaka: ৳ 130.0</span>
                                        <span class="fw-semibold mt-1">Shipping to Dhaka.</span>
                                        <a href="#" class="text-decoration-none mt-1">Change address</a>
                                    </div>
                                </div>
                                <div class="spotlight_cart_total_row border-top pt-3">
                                    <h6 class="fw-bold mb-0">Total</h6>
                                    <h6 class="fw-bold mb-0">৳ 2,439.0</h6>
                                </div>
                                <button class="btn btn-dark spotlight_cart_action_button mt-3">Proceed To
                                    Checkout</button>
                            </div>

                            <!-- Delivery & Return Section -->
                            <div class="spotlight_cart_summary_card">
                                <h5 class="fw-semibold mb-3">Delivery & Return</h5>
                                <div class="accordion" id="deliveryAccordion">
                                    <div class="accordion-item spotlight_cart_accordion_item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed small fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="false" aria-controls="collapseOne">
                                                My order hasn’t arrived yet. Where is it?
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#deliveryAccordion">
                                            <div class="accordion-body small text-muted">
                                                You can track your order using the tracking number provided in your
                                                shipping confirmation email. Please allow 2-3 business days for
                                                processing and shipping.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item spotlight_cart_accordion_item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed small fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                How can you evaluate content without design? No typography, no colors,
                                                no layout, no styles, all those things that convey the important signals
                                                that go beyond the mere textual, hierarchies of information, weight.
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo" data-bs-parent="#deliveryAccordion">
                                            <div class="accordion-body small text-muted">
                                                This is a placeholder answer. The question itself is a bit of a
                                                placeholder, so you'd want to replace both the question and the answer
                                                with real content.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item spotlight_cart_accordion_item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed small fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                                Do you deliver on public holidays?
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            aria-labelledby="headingThree" data-bs-parent="#deliveryAccordion">
                                            <div class="accordion-body small text-muted">
                                                Deliveries are not made on public holidays. Please check our delivery
                                                schedule for more information.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item spotlight_cart_accordion_item">
                                        <h2 class="accordion-header" id="headingFour">
                                            <button class="accordion-button collapsed small fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseFour"
                                                aria-expanded="false" aria-controls="collapseFour">
                                                Do you deliver to my postcode?
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse"
                                            aria-labelledby="headingFour" data-bs-parent="#deliveryAccordion">
                                            <div class="accordion-body small text-muted">
                                                Please enter your postcode at checkout to see if we deliver to your
                                                area.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item spotlight_cart_accordion_item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button collapsed small fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                Is next-day delivery available on all orders?
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse"
                                            aria-labelledby="headingFive" data-bs-parent="#deliveryAccordion">
                                            <div class="accordion-body small text-muted">
                                                Next-day delivery is available for most orders placed before our cutoff
                                                time. Some exclusions may apply.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item spotlight_cart_accordion_item">
                                        <h2 class="accordion-header" id="headingSix">
                                            <button class="accordion-button collapsed small fw-semibold" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseSix"
                                                aria-expanded="false" aria-controls="collapseSix">
                                                Do I need to be there to sign for delivery?
                                            </button>
                                        </h2>
                                        <div id="collapseSix" class="accordion-collapse collapse"
                                            aria-labelledby="headingSix" data-bs-parent="#deliveryAccordion">
                                            <div class="accordion-body small text-muted">
                                                A signature may be required for some deliveries. Please check the
                                                tracking information for details.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
@endsection

@section('script')

@endsection
