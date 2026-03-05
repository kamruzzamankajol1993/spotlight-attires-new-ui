@extends('front.master.master')
@section('title', 'Order Successful')

@section('body')
<main>
    <section class="section">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 text-center">
                     <div class="card shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            <h1 class="mt-3">Thank You!</h1>
                            <p class="lead">Your order has been placed successfully.</p>
                            <hr class="my-4">
                            <p class="mb-1">Your Order ID is:</p>
                            <h4 class="fw-bold">#{{ $order->invoice_no }}</h4>
                            {{-- <p class="small text-muted mt-3">We have sent a confirmation email to <strong>{{ $order->customer->email }}</strong> with your order details.</p> --}}
                            <div class="mt-4">
                                <a href="{{ route('shop.show') }}" class="btn btn-outline-secondary">Continue Shopping</a>
                                <a href="{{ route('user.order.list') }}" class="btn btn-dark">View My Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection


@section('script')
{{-- ডুপ্লিকেট ট্র্যাকিং রোধ করতে কন্ডিশন: কন্ট্রোলার থেকে আসা wasTracked যদি ০ হয় তবেই চলবে --}}
@if(isset($wasTracked) && $wasTracked == 0)
<script>
    // ১. গুগল ট্যাগ ম্যানেজার (GTM) পারচেজ ইভেন্ট
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        'event': 'purchase',
        'ecommerce': {
            'transaction_id': '{{ $order->invoice_no }}', {{-- আপনার মডেলে থাকা ইউনিক ইনভয়েস নম্বর --}}
            'affiliation': 'Online Store',
            'value': {{ number_format($order->total_amount, 2, '.', '') }},
            'tax': 0,
            'shipping': {{ number_format($order->shipping_cost, 2, '.', '') }},
            'currency': 'BDT',
            'items': [
                @foreach($order->orderDetails as $item)
                {
                    'item_id': '{{ $item->product_id }}',
                    'item_name': '{{ $item->product->name ?? "Product" }}',
                    'item_category': '{{ $item->product->category->name ?? "General" }}',
                    'price': {{ number_format($item->unit_price, 2, '.', '') }},
                    'quantity': {{ $item->quantity }}
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
        }
    });

    // ২. ফেসবুক পিক্সেল (Meta Pixel) পারচেজ ইভেন্ট
    if (typeof fbq !== 'undefined') {
        fbq('track', 'Purchase', {
            content_ids: [@foreach($order->orderDetails as $item)'{{ $item->product_id }}'{{ !$loop->last ? ',' : '' }}@endforeach],
            content_type: 'product',
            value: {{ number_format($order->total_amount, 2, '.', '') }},
            currency: 'BDT'
        }, {eventID: 'order_{{ $order->id }}'}); {{-- Deduplication এর জন্য eventID --}}
    }

    console.log("Tracking events fired: GTM & Facebook Pixel");
</script>
@endif
@endsection