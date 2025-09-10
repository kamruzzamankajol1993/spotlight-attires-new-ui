<div class="container-fluid" id="quick-view-container-{{ $product->id }}">
    <div class="row">
        <div class="col-md-5">
            <img id="quick-view-image" src="{{ (is_array($product->main_image) && count($product->main_image) > 0) ? $front_ins_url .'public/uploads/' . $product->main_image[0] : 'https://placehold.co/400' }}" alt="{{ $product->name }}" class="img-fluid rounded-3 w-100">
        </div>

        <div class="col-md-7">
            <h3 class="fw-semibold">{{ $product->name }}</h3>
            <p class="text-muted">SKU: <span id="quick-view-sku">{{ $product->product_code }}</span></p>

            <div class="mb-3">
                @if($product->discount_price)
                    <span class="h4 fw-bold text-dark" id="quick-view-price">৳ {{ number_format($product->discount_price, 2) }}</span>
                    <del class="text-muted ms-2">৳ {{ number_format($product->base_price, 2) }}</del>
                @else
                    <span class="h4 fw-bold text-dark" id="quick-view-price">৳ {{ number_format($product->base_price, 2) }}</span>
                @endif
            </div>

            @if($product->variants->isNotEmpty() && $product->variants->first()->color)
                <div class="mb-3">
                    <h6 class="fw-semibold">Color: <span id="quick-view-color-name">{{ $product->variants->first()->color->name }}</span></h6>
                    <div class="d-flex gap-2">
                        @foreach($product->variants as $variant)
                            <div class="color-option {{ $loop->first ? 'active' : '' }}"
                                 style="background-color: {{ $variant->color->code }}; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; border: 2px solid #ddd;"
                                 data-variant-id="{{ $variant->id }}"
                                 data-color-name="{{ $variant->color->name }}"
                                 data-additional-price="{{ $variant->additional_price }}"
                                 data-variant-sku="{{ $variant->variant_sku }}"
                                 data-sizes="{{ json_encode($variant->detailed_sizes) }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <h6 class="fw-semibold">Size: <span id="quick-view-size-name" class="text-dark">Select a size</span></h6>
                <div id="quick-view-size-container" class="d-flex gap-2 flex-wrap">
                    {{-- Size buttons will be dynamically inserted here --}}
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center border rounded-3 overflow-hidden">
                    <button class="btn btn-light rounded-0" id="qv-quantity-minus">-</button>
                    <span class="px-3" id="qv-quantity-value">1</span>
                    <button class="btn btn-light rounded-0" id="qv-quantity-plus">+</button>
                </div>
                <button class="btn btn-dark fw-semibold flex-grow-1" id="qv-add-to-cart">Add To Cart</button>
                <button class="btn btn-outline-danger" id="qv-add-to-wishlist" title="Add to Wishlist">
                    <i class="bi bi-heart-fill"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const container = $('#quick-view-container-{{ $product->id }}');
    const BASE_PRODUCT_PRICE = {{ $product->discount_price ?? $product->base_price }};
    let selectedVariantId = null;
    let selectedSize = null;

    function updateSizes(sizes) {
        const sizeContainer = container.find('#quick-view-size-container');
        sizeContainer.empty();
        container.find('#quick-view-size-name').text('Select a size');
        selectedSize = null;

        if (sizes && sizes.length > 0) {
            sizes.forEach(size => {
                const button = $('<button></button>')
                    .addClass('btn btn-outline-secondary btn-sm size-option')
                    .text(size.name)
                    .data('size-name', size.name);
                
                if (size.quantity <= 0) {
                    button.prop('disabled', true).css('text-decoration', 'line-through');
                }
                sizeContainer.append(button);
            });
        } else {
            sizeContainer.html('<p class="text-danger small">This color is out of stock.</p>');
        }
    }

    // Handle color click
    container.find('.color-option').on('click', function() {
        const $this = $(this);
        container.find('.color-option').removeClass('active').css({'border-color': '#ddd', 'transform': 'scale(1)'});
        $this.addClass('active').css({'border-color': '#000', 'transform': 'scale(1.15)'});

        selectedVariantId = $this.data('variant-id');
        container.find('#quick-view-color-name').text($this.data('color-name'));
        container.find('#quick-view-sku').text($this.data('variant-sku') || '{{ $product->product_code }}');
        
        const additionalPrice = parseFloat($this.data('additional-price') || 0);
        const finalPrice = BASE_PRODUCT_PRICE + additionalPrice;
        container.find('#quick-view-price').text(`৳ ${finalPrice.toFixed(2)}`);
        
        updateSizes($this.data('sizes'));
    });

    // Handle size click
    container.find('#quick-view-size-container').on('click', '.size-option:not(:disabled)', function() {
        const $this = $(this);
        container.find('#quick-view-size-container .size-option').removeClass('active').css({'background-color': '', 'color': ''});
        $this.addClass('active').css({'background-color': '#212529', 'color': '#fff'});
        selectedSize = $this.data('size-name');
        container.find('#quick-view-size-name').text(selectedSize);
    });

    // Handle quantity
    container.find('#qv-quantity-plus').on('click', () => {
        let qty = parseInt(container.find('#qv-quantity-value').text());
        container.find('#qv-quantity-value').text(++qty);
    });
    container.find('#qv-quantity-minus').on('click', () => {
        let qty = parseInt(container.find('#qv-quantity-value').text());
        if (qty > 1) {
            container.find('#qv-quantity-value').text(--qty);
        }
    });

    // Handle Add to Cart
    container.find('#qv-add-to-cart').on('click', function() {
         if (!selectedVariantId) {
            Swal.fire({
              icon: 'warning',
              title: 'Hold on!',
              text: 'Please select a color first.'
            });
            return;
        }
        if (!selectedSize) {
            Swal.fire({
              icon: 'warning',
              title: 'Almost there!',
              text: 'Please select a size.'
            });
            return;
        }

        const $button = $(this);
        const cartData = {
            productId: {{ $product->id }},
            variantId: selectedVariantId,
            size: selectedSize,
            quantity: parseInt(container.find('#qv-quantity-value').text()),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: '{{ route("cart.add") }}',
            type: 'POST',
            data: cartData,
            beforeSend: function() {
                $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Adding...');
            },
            success: function(response) {
                if (response.success) {
                    // 1. Directly update the sidebar cart with the new HTML
                    $('#cartOffcanvas .cart-products').html(response.sidebar_html);
                    $('#cart-subtotal').text('৳ ' + response.subtotal);
                    $('#desktop-cart-count').text(response.count);
                    $('#mobile-cart-count').text(response.count);

                    // 2. Trigger the global event and PASS the data to it
                    // This allows the main cart page to update without another AJAX call
                    $(document.body).trigger('cart-updated', [response]);

                    // 3. Show success feedback and open the sidebar
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });

                    $('#quickViewModal').modal('hide');
                    const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
                    cartOffcanvas.show();
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: response.message || 'An error occurred.' });
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Request Failed', text: 'Could not add product to cart.' });
            },
            complete: function() {
                $button.prop('disabled', false).text('Add To Cart');
            }
        });
    });

     // --- NEW: Handle Add to Wishlist ---
    container.find('#qv-add-to-wishlist').on('click', function() {
        @auth
            // --- USER IS LOGGED IN ---
            if (!selectedVariantId) {
                Swal.fire({ icon: 'warning', title: 'Hold on!', text: 'Please select a color first.' });
                return;
            }
            if (!selectedSize) {
                Swal.fire({ icon: 'warning', title: 'Almost there!', text: 'Please select a size.' });
                return;
            }

            const $button = $(this);
            const wishlistData = {
                product_id: {{ $product->id }},
                variant_id: selectedVariantId,
                size: selectedSize,
                _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: '{{ route("wishlist.add") }}',
                type: 'POST',
                data: wishlistData,
                beforeSend: function() {
                    $button.prop('disabled', true).find('i').toggleClass('bi-heart-fill bi-arrow-clockwise');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: response.message, showConfirmButton: false, timer: 2000 });
                    } else {
                         Swal.fire({ icon: 'info', title: 'Already Added', text: response.message });
                    }
                },
                error: function(xhr) {
                     Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong. Please try again.' });
                },
                complete: function() {
                    $button.prop('disabled', false).find('i').toggleClass('bi-arrow-clockwise bi-heart-fill');
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

    // Trigger click on the first color to initialize sizes
    container.find('.color-option.active').first().trigger('click');
});
</script>