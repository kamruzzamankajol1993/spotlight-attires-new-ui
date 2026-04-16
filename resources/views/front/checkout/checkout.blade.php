@extends('front.master.master')
@section('title', 'Checkout')

@section('css')
<style>
    /* Custom Checkbox Styles */
    .custom-checkbox-card { border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem; cursor: pointer; transition: all 0.2s ease-in-out; position: relative; }
    .custom-checkbox-card:hover { border-color: #000; background-color: #f8f9fa;}
    .custom-checkbox-card input[type="radio"] { opacity: 0; position: absolute; }
    .custom-checkbox-card.selected { border-color: #0d6efd; background-color: #e7f1ff; box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25); }
    .custom-checkbox-card .icon { font-size: 1.5rem; }
    .custom-checkbox-card .title { font-weight: 600; }
    .custom-checkbox-card .description { font-size: 0.85rem; color: #6c757d; }
    
    .custom-checkbox-card.disabled-option {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #f8f9fa !important;
    }
    .custom-checkbox-card.disabled-option:hover {
        border-color: #dee2e6; /* Prevents hover effect */
        box-shadow: none;
    }
</style>
@endsection

@section('body')
<main>
    <section class="section">
        <div class="container">
            <div class="spotlight_checkout_container">

                {{-- Section to display validation errors and session messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                     <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form id="checkout-form" method="POST" action="{{ route('place.order') }}">
                    @csrf
                    {{-- Hidden input to hold the shipping cost --}}
                    <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="0">
                    
                    <div class="row">
                        {{-- Left Column: Billing Details --}}
                        <div class="col-lg-7 mb-4">
                            <h3 class="spotlight_checkout_section-title">BILLING DETAILS</h3>
                             <div class="row">
                                <div class="col-md-6">
                                    <div class="spotlight_checkout_form-group">
                                        <label for="name">Full Name *</label>
                                        <input type="text" id="name" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="spotlight_checkout_form-group">
                                        <label for="phone">Phone *</label>
                                        <input type="tel" id="phone" class="form-control" value="{{ Auth::user()->phone }}" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="spotlight_checkout_form-group mt-3">
                                <label class="mb-2">Select Shipping Address *</label>
                                @forelse($addresses as $address)
                                <label class="d-block">
                                    <div class="form-check border p-3 rounded-3 mb-2 custom-checkbox-card" data-name="shipping_address">
                                        <input class="form-check-input shipping-address-radio" type="radio" name="shipping_address_id" id="address{{ $address->id }}" value="{{ $address->id }}" {{ $address->is_default ? 'checked' : '' }}>
                                        <div class="w-100">
                                            <strong>{{ $address->name }}</strong> ({{ $address->address_type }})<br>
                                            <small>{{ $address->address }}</small><br>
                                            <small>{{ $address->phone }}</small>
                                        </div>
                                    </div>
                                </label>
                                @empty
                                <div class="alert alert-warning">
                                    You have no saved addresses. Please <a href="{{ route('dashboard.profile.address.update') }}">add an address</a> first.
                                </div>
                                @endforelse
                                <a href="{{ route('dashboard.profile.address.update') }}" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-plus-circle"></i> Add or Manage Addresses</a>
                            </div>

                            <div class="spotlight_checkout_form-group mt-4">
                                <label for="order-notes">Order notes (optional)</label>
                                <textarea id="order-notes" name="notes" class="form-control" rows="4" placeholder="Notes about your order..."></textarea>
                            </div>
                        </div>

                        {{-- Right Column: Order Summary --}}
                        <div class="col-lg-5">
                            
                            {{-- Reward Points Logic & UI --}}
                            @php
                                $rewardSettings = \App\Models\RewardPointSetting::first();
                                
                                // Dynamic Calculation from RewardPoint Table
                                $customerId = Auth::user()->customer->id;
                                $earnedPoints = \App\Models\RewardPoint::where('customer_id', $customerId)->where('type', 'earned')->sum('points');
                                $redeemedPoints = \App\Models\RewardPoint::where('customer_id', $customerId)->where('type', 'redeemed')->sum('points');
                                $customerPoints = $earnedPoints - $redeemedPoints;

                                // Calculate potential value roughly for display
                                $potentialValue = 0;
                                if($rewardSettings && $rewardSettings->redeem_points_per_unit > 0) {
                                    $potentialValue = floor($customerPoints / $rewardSettings->redeem_points_per_unit) * $rewardSettings->redeem_per_unit_amount;
                                }
                                
                                // Check if reward points are already applied in session
                                $rewardSession = Session::get('reward_point_discount');
                                $isRewardApplied = $rewardSession ? true : false;
                                $rewardDiscountAmount = $rewardSession['amount'] ?? 0;
                            @endphp

                            @if($rewardSettings && $rewardSettings->is_enabled && $customerPoints >= $rewardSettings->redeem_points_per_unit)
                            <div class="card mb-4 border-warning">
                                <div class="card-body p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-star-fill text-warning me-2"></i>Reward Points</h6>
                                        <span class="badge bg-warning text-dark">{{ $customerPoints }} Points</span>
                                    </div>
                                    <p class="small text-muted mb-2">
                                        You have <strong>{{ $customerPoints }}</strong> points (Value: ~৳{{ $potentialValue }}).
                                    </p>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="redeemPointsCheckbox" {{ $isRewardApplied ? 'checked' : '' }}>
                                        <label class="form-check-label small fw-semibold" for="redeemPointsCheckbox">Redeem Points for Discount</label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            {{-- End Reward Points Section --}}

                            <div class="spotlight_checkout_order-summary">
                                <div class="mb-4">
                                    <h5 class="mb-3">Delivery Type</h5>
                                    <p class="text-muted small mb-2">Express delivery available only inside Dhaka .</p>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="custom-checkbox-card" data-name="delivery_type">
                                                <input type="radio" name="delivery_type" value="regular" checked>
                                                <div class="d-flex align-items-center"><i class="bi bi-truck icon me-3"></i><div><div class="title">Regular</div><div class="description">3-5 days</div></div></div>
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <label id="express-delivery-option" class="custom-checkbox-card" data-name="delivery_type">
                                                <input id="express-delivery-input" type="radio" name="delivery_type" value="express">
                                                <div class="d-flex align-items-center"><i class="bi bi-lightning-charge-fill icon me-3"></i><div><div class="title">Express</div><div class="description">1-2 days</div></div></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <h3 class="spotlight_checkout_section-title">YOUR ORDER</h3>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr><th>PRODUCT</th><th class="text-end">SUBTOTAL</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cartItems as $item)
                                            <tr>
                                                <td>{{ Str::limit($item['name'], 25) }} &times; {{ $item['quantity'] }} <div class="flex-grow-1">
    {{-- আপনা বিদ্যমান পরোডাক্ট না এবং Qty এর নিচে এটি বসান --}}
    
    @if(isset($item['is_custom']) && $item['is_custom'])
        <div class="p-1 mt-1 border-start border-primary border-2 ps-2" style="background-color: #f9f9f9;">
            <span class="d-block text-primary fw-bold" style="font-size: 10px; text-transform: uppercase;">
                Customization Details:
            </span>
            <span class="small text-dark" style="font-size: 11px;">
                Name: {{ $item['custom_name'] }} | Number: {{ $item['custom_number'] }}
            </span>
        </div>
    @endif
</div></td>
                                                <td class="text-end fw-bold">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
{{-- checkout.blade.php --}}


                                <div class="spotlight_checkout_subtotal"><span>Subtotal</span><span>{{ number_format($subtotal, 2) }}</span></div>
                                
                                {{-- DISCOUNT DISPLAY --}}
                                @if($discount > 0)
                                    <div class="spotlight_checkout_subtotal text-success">
                                        <span>
                                            @if($coupon)
                                                Coupon Discount ({{ $coupon->code }})
                                            @else
                                                Customer Discount ({{ Auth::user()->customer->discount_in_percent ?? 0 }}%)
                                            @endif
                                        </span>
                                        <span>- ৳{{ number_format($discount, 2) }}</span>
                                    </div>
                                @endif

                                {{-- REWARD POINT DISCOUNT DISPLAY --}}
                                @if($rewardDiscountAmount > 0)
                                    <div class="spotlight_checkout_subtotal text-warning">
                                        <span>Reward Points Discount</span>
                                        <span>- ৳{{ number_format($rewardDiscountAmount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="spotlight_checkout_subtotal"><span>Shipping</span><span id="shipping-charge-text">Select an address</span></div>
                                
                                {{-- FINAL TOTAL CALCULATION --}}
                                @php
                                    $finalSubtotal = $subtotal - $discount - $rewardDiscountAmount;
                                    if($finalSubtotal < 0) $finalSubtotal = 0;
                                @endphp
                                
                                <div class="spotlight_checkout_total"><span>Total</span><span id="grand-total-text">৳{{ number_format($finalSubtotal, 2) }}</span></div>
                                
                                <div class="my-4">
                                    <h5 class="mb-3">Payment Method</h5>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="custom-checkbox-card" data-name="payment_method"><input type="radio" name="payment_method" value="cod" checked><div class="d-flex align-items-center"><i class="bi bi-cash-coin icon me-3"></i><div><div class="title">Cash on Delivery</div><div class="description">Pay upon arrival</div></div></div></label>
                                        </div>
                                        <div class="col-6">
                                            <label class="custom-checkbox-card disabled-option" data-name="payment_method">
                                                <input type="radio" name="payment_method" value="sslcommerz" disabled>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-credit-card-2-front-fill icon me-3"></i>
                                                    <div>
                                                        <div class="title">SSLCommerz</div>
                                                        <div class="description">Card, MFS, Banking</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                         <div class="col-6 mt-2">
                                            <label class="custom-checkbox-card" data-name="payment_method"><input type="radio" name="payment_method" value="bkash"><div class="d-flex align-items-center"><i class="bi bi-wallet2 icon me-3"></i><div><div class="title">bKash</div><div class="description">Pay with bKash</div></div></div></label>
                                        </div>
                                    </div>
                                </div>
                                <p class="spotlight_checkout_privacy-text small">Your personal data will be used to process your order...</p>
                                <button type="submit" class="spotlight_checkout_place-order-btn" id="place-order-btn" disabled>Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        try {
            // ১. ডাটা প্রিপারেশন (কন্ট্রোলার থেকে আসা $cartItems ব্যবহার করে)
            var checkoutProducts = [
                @if(isset($cartItems) && count($cartItems) > 0)
                    @foreach($cartItems as $item)
                    {
                        'item_id': '{{ $item['product_id'] ?? ($item['id'] ?? '') }}',
                        'item_name': '{{ addslashes($item['name'] ?? 'Product') }}',
                        'price': parseFloat("{{ $item['price'] ?? 0 }}") || 0,
                        'quantity': parseInt("{{ $item['quantity'] ?? 1 }}") || 1
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                @endif
            ];
            
            // সাবটোটাল বা টোটাল ভ্যালু
            var totalValue = parseFloat("{{ $subtotal ?? 0 }}") || 0;

            // ২. Meta Pixel (InitiateCheckout)
            if (typeof fbq === 'function') {
                fbq('track', 'InitiateCheckout', {
                    content_ids: checkoutProducts.map(function(item) { return item.item_id; }),
                    content_type: 'product',
                    value: totalValue,
                    currency: 'BDT'
                });
                console.log("Meta Pixel: InitiateCheckout tracked successfully.");
            }

            // ৩. Google Tag Manager (begin_checkout)
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'event': 'begin_checkout',
                'ecommerce': {
                    'currency': 'BDT',
                    'value': totalValue,
                    'items': checkoutProducts
                }
            });
            console.log("GTM: begin_checkout event pushed.");

        } catch (e) {
            console.warn("Checkout Tracking Error:", e);
        }
    });
</script>
<script>
$(document).ready(function() {
    // Initial base amount from server-side calculation
    let subtotalWithDiscount = {{ $finalSubtotal }};
    let shippingCharge = 0;

    function updateTotals() {
        const grandTotal = subtotalWithDiscount + shippingCharge;
        $('#shipping-charge-text').text(`৳${shippingCharge.toFixed(2)}`);
        $('#grand-total-text').text(`৳${grandTotal.toFixed(2)}`);

        // Update the hidden input's value
        $('#shipping_cost_input').val(shippingCharge);

        $('#place-order-btn').prop('disabled', false);
    }

    // এই অংশটি আপনার existing scripts এর ভেতর আপডেট করুন

function getShippingCharge(addressId) {
    if (!addressId) {
        $('#shipping-charge-text').text('Select an address');
        $('#place-order-btn').prop('disabled', true);
        return;
    }

    // ডেলিভারি টাইপ ভ্যালু গেট করা
    const deliveryType = $('input[name="delivery_type"]:checked').val();

    // --- EXPRESS DELIVERY UI LOGIC ---
    const expressOption = $('#express-delivery-option');
    const expressInput = $('#express-delivery-input');
    const regularInput = $('input[name="delivery_type"][value="regular"]');
    
    const selectedRadio = $(`.shipping-address-radio[value="${addressId}"]`);
    const addressText = selectedRadio.closest('.form-check').find('small').first().text();
    const isDhaka = addressText.toLowerCase().includes('dhaka');

    if (isDhaka) {
        expressOption.removeClass('disabled-option');
        expressInput.prop('disabled', false);
    } else {
        expressOption.addClass('disabled-option');
        expressInput.prop('disabled', true);
        if (expressInput.is(':checked')) {
            regularInput.prop('checked', true).parent().addClass('selected');
            expressInput.parent().removeClass('selected');
        }
    }

    $('#shipping-charge-text').html('<span class="spinner-border spinner-border-sm"></span>');
    $('#place-order-btn').prop('disabled', true);

    $.ajax({
        url: '{{ route("get.shipping.charge") }}',
        method: 'POST',
        data: { 
            _token: '{{ csrf_token() }}', 
            address_id: addressId,
            delivery_type: deliveryType // নতুন ডাটা পাঠানো হচ্ছে
        },
        success: function(response) {
            if(response.success) {
                shippingCharge = parseFloat(response.shipping_charge);
                updateTotals();
            }
        },
        error: function(xhr) {
            shippingCharge = 130; 
            updateTotals();
        }
    });
}

// নতুন ইভেন্ট লিসেনার: ডেলিভারি টাইপ চেঞ্জ হলে চার্জ আপডেট হবে
$('input[name="delivery_type"]').on('change', function() {
    const addressId = $('.shipping-address-radio:checked').val();
    if (addressId) {
        getShippingCharge(addressId);
    }
});

    // --- REWARD POINTS TOGGLE HANDLER ---
    $('#redeemPointsCheckbox').on('change', function() {
        const isChecked = $(this).is(':checked');
        const url = isChecked ? '{{ route("checkout.apply_points") }}' : '{{ route("checkout.remove_points") }}';

        // Show loading state
        $('#grand-total-text').html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if(response.success) {
                    // Reload page to refresh all server-side calculations
                    location.reload(); 
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.message });
                    $('#redeemPointsCheckbox').prop('checked', !isChecked);
                    updateTotals(); // Reset text
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                $('#redeemPointsCheckbox').prop('checked', !isChecked);
                updateTotals();
            }
        });
    });

    // --- STANDARD EVENTS ---

    $('.custom-checkbox-card').on('click', function() {
        if ($(this).hasClass('disabled-option')) return;
        
        const radioName = $(this).find('input[type="radio"]').attr('name');
        $(`.custom-checkbox-card input[name="${radioName}"]`).closest('.custom-checkbox-card').removeClass('selected');
        $(this).addClass('selected').find('input[type="radio"]').prop('checked', true).trigger('change');
    });
    
    $('input[type="radio"]:checked').closest('.custom-checkbox-card').addClass('selected');

    $('.shipping-address-radio').on('change', function() {
        getShippingCharge($(this).val());
    });

    // --- INITIAL LOAD LOGIC (Fixed) ---
    const defaultAddressRadio = $('.shipping-address-radio:checked');
    if (defaultAddressRadio.length > 0) {
        getShippingCharge(defaultAddressRadio.val());
    } else {
        const firstRadio = $('.shipping-address-radio').first();
        if (firstRadio.length > 0) {
            firstRadio.prop('checked', true).trigger('change');
        } else {
            $('#shipping-charge-text').text('Please add an address');
            $('#place-order-btn').prop('disabled', true);
        }
    }
});
</script>
@endsection