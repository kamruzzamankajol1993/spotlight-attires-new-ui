@extends('front.master.master')

@section('title', 'Home')
@section('css')
@endsection
@section('body')
    <main>
        <section class="spotlighthero hero-section">
            <div class="container">
                <div class="row">
                    <!-- Left side: Slider -->
                    <div class="col-lg-7 mb-3 mb-lg-0">
                        <div class="main-slider">
                           @if(isset($latestProducts) && $latestProducts->count() > 0)
            @foreach($latestProducts as $product)
                <div class="slider-item">
                    {{-- Use the first thumbnail image, or a placeholder --}}
                    @php

                    $mobileImage = (is_array($product->thumbnail_image) && count($product->thumbnail_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->thumbnail_image[0]
                            : 'https://placehold.co/800x400';
                        $desktopImage = (is_array($product->main_image) && count($product->main_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->main_image[0]
                            : 'https://placehold.co/800x400';
                    @endphp
                    <picture>
            <!-- This source will be used on screens 992px wide or larger (desktops/laptops) -->
            <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
            
            <!-- This is the default image that will be used on smaller screens (mobile) -->
            <img src="{{ $mobileImage }}" alt="{{ $product->name }}">
        </picture>
                    <div class="content">
                        <h1 class="fw-bold">{{ Str::upper($product->name) }}</h1>
                        {{-- You can use the product description or category name here --}}
                        <p>{{ $product->category->name ?? 'New Arrival' }}</p>
                        <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-light">ORDER NOW</a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="slider-item">
                <img src="{{asset('/')}}public/front/assets/img/slider/banner.jpg" alt="Default Banner">
                <div class="content">
                    <h1 class="fw-bold">WELCOME</h1>
                    <p>Check out our latest collections.</p>
                    <button class="btn btn-outline-light">SHOP NOW</button>
                </div>
            </div>
        @endif
                        </div>
                    </div>

                    <!-- Right side: Banners -->
                    <div class="col-lg-5">
                        <!-- Top banner -->
                           @if($topBannerProduct)
        <div class="banner-item top-banner mb-3">
              @php

                    $mobileImage = (is_array($topBannerProduct->thumbnail_image) && count($topBannerProduct->thumbnail_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $topBannerProduct->thumbnail_image[0]
                            : 'https://placehold.co/800x400';
                        $desktopImage = (is_array($topBannerProduct->main_image) && count($topBannerProduct->main_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $topBannerProduct->main_image[0]
                            : 'https://placehold.co/800x400';
                    @endphp
                    <picture>
            <!-- This source will be used on screens 992px wide or larger (desktops/laptops) -->
            <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
            
            <!-- This is the default image that will be used on smaller screens (mobile) -->
            <img src="{{ $mobileImage }}" alt="{{ $topBannerProduct->name }}">
        </picture>
            <div class="content">
                <h4 class="fw-bold">{{ Str::upper(Str::limit($topBannerProduct->name, 20)) }}</h4>
                <p>{{ $topBannerProduct->category->name ?? 'Featured Item' }}</p>
                <a href="{{ route('product.show', $topBannerProduct->slug) }}" class="btn btn-outline-light">VIEW DETAILS</a>
            </div>
        </div>
    @endif

                          @if($bottomBannerProducts->count() > 0)
        <div class="row">
            @foreach($bottomBannerProducts as $bottomProduct)
                <div class="col-6 {{ $loop->first ? 'pe-2' : 'ps-2' }}">
                    <div class="banner-item bottom-banner">
                            @php

                    $mobileImage = (is_array($bottomProduct->thumbnail_image) && count($bottomProduct->thumbnail_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $bottomProduct->thumbnail_image[0]
                            : 'https://placehold.co/800x400';
                        $desktopImage = (is_array($bottomProduct->main_image) && count($bottomProduct->main_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $bottomProduct->main_image[0]
                            : 'https://placehold.co/800x400';
                    @endphp
                    <picture>
            <!-- This source will be used on screens 992px wide or larger (desktops/laptops) -->
            <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
            
            <!-- This is the default image that will be used on smaller screens (mobile) -->
            <img src="{{ $mobileImage }}" alt="{{ $bottomProduct->name }}">
        </picture>
                        <div class="content">
                            <h5 class="fw-bold">{{ Str::upper(Str::limit($bottomProduct->name, 18)) }}</h5>
                            <p>LIMITED OFFER</p>
                            <a href="{{ route('product.show', $bottomProduct->slug) }}" class="btn btn-outline-light btn-sm">SHOP NOW</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="product-section">
                <div class="container">
                    <h2 class="mb-4 text-center">Top Rated Products</h2>
                    <div class="product-slider">
                       @if(isset($products) && $products->count() > 0)
    @foreach($products as $product)
    <div class="product-card card">
        @php
            $mobileImage = (is_array($product->thumbnail_image) && count($product->thumbnail_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->thumbnail_image[0]
                            : 'https://placehold.co/800x400';
                        $desktopImage = (is_array($product->main_image) && count($product->main_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->main_image[0]
                            : 'https://placehold.co/800x400';

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
<a href="{{ route('product.show', $product->slug) }}">
        {{-- Picture element for responsive images --}}
    <picture>
        {{-- Desktop and laptop (≥992px) --}}
        <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
        {{-- Tablet and mobile (<992px) --}}
        <source media="(max-width: 991px)" srcset="{{ $mobileImage }}">
        {{-- Fallback for browsers without <picture> support --}}
        <img src="{{ $mobileImage }}" 
             alt="{{ $product->name }}" 
             class="card-img-top img-fluid">
    </picture>
</a>
        <div class="product-details-body">
            <h5 class="product-title mb-1">{{ Str::limit($product->name, 25) }}</h5>
            <p class="product-meta mb-1">Category: {{ $product->category->name ?? 'N/A' }}</p>
            <p class="product-meta mb-1">SKU: {{ $product->product_code ?? 'N/A' }}</p>

            @if($totalStock > 0)
                <p class="product-meta text-success fw-bold mb-1"><i class="bi bi-check-circle-fill"></i> In stock</p>
            @else
                <p class="product-meta text-danger fw-bold mb-1"><i class="bi bi-x-circle-fill"></i> Out of stock</p>
            @endif

            <div class="rating-stars mb-2">
                {{-- You can make this dynamic if you add a rating column --}}
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
            </div>

            <p class="price-tag mb-2">
                @if($product->discount_price)
                    <del class="text-muted">৳ {{ $product->base_price }}</del>
                    <span class="fw-bold">৳ {{ $product->discount_price }}</span>
                @else
                    <span class="fw-bold">৳ {{ $product->base_price }}</span>
                @endif
            </p>
            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
        </div>
    </div>
    @endforeach
@endif
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="featured-product-section">
                <div class="container">
                    <div class="row">
                        <!-- Left side: Banner -->
                        <div class="col-lg-4 mb-4 mb-lg-0 d-flex">
                            <div class="featured-banner flex-grow-1">
                                <img src="{{asset('/')}}public/front/assets/img/slider/banner1.jpg" alt="Men's Premium T-Shirt">
                                <div class="content">
                                    <h4 class="mb-3">MEN'S PREMIUM<br>ACID WASH</h4>
                                    <p class="mb-4">"Itachi Uchiha <br>Retro vibes and faded dreams—this acid wash tee
                                        brings the vintage feels to your closet."</p>
                                    <a href="#" class="btn btn-outline-light">Buy Now</a>
                                </div>
                            </div>
                        </div>
                        <!-- Right side: Product Slider -->
                        <div class="col-lg-8">
                            <h2 class="mb-4 text-center">ALL COLLECTIONS</h2>
                            <div class="product-carousel">
                              @if(isset($randomLatestProducts) && $randomLatestProducts->count() > 0)
    @foreach($randomLatestProducts as $product)
    <div class="product-card card">
        @php
             $mobileImage = (is_array($product->thumbnail_image) && count($product->thumbnail_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->thumbnail_image[0]
                            : 'https://placehold.co/800x400';
                        $desktopImage = (is_array($product->main_image) && count($product->main_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->main_image[0]
                            : 'https://placehold.co/800x400';

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
<a href="{{ route('product.show', $product->slug) }}">
        {{-- Picture element for responsive images --}}
    <picture>
        {{-- Desktop and laptop (≥992px) --}}
        <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
        {{-- Tablet and mobile (<992px) --}}
        <source media="(max-width: 991px)" srcset="{{ $mobileImage }}">
        {{-- Fallback for browsers without <picture> support --}}
        <img src="{{ $mobileImage }}" 
             alt="{{ $product->name }}" 
             class="card-img-top img-fluid">
    </picture>
    </a>
        <div class="product-details-body">
            <h5 class="product-title mb-1">{{ Str::limit($product->name, 25) }}</h5>
            <p class="product-meta mb-1">Category: {{ $product->category->name ?? 'N/A' }}</p>
            <p class="product-meta mb-1">SKU: {{ $product->product_code ?? 'N/A' }}</p>

            @if($totalStock > 0)
                <p class="product-meta text-success fw-bold mb-1"><i class="bi bi-check-circle-fill"></i> In stock</p>
            @else
                <p class="product-meta text-danger fw-bold mb-1"><i class="bi bi-x-circle-fill"></i> Out of stock</p>
            @endif

            <div class="rating-stars mb-2">
                {{-- You can make this dynamic if you add a rating column --}}
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
            </div>

            <p class="price-tag mb-2">
                @if($product->discount_price)
                    <del class="text-muted">৳ {{ $product->base_price }}</del>
                    <span class="fw-bold">৳ {{ $product->discount_price }}</span>
                @else
                    <span class="fw-bold">৳ {{ $product->base_price }}</span>
                @endif
            </p>
            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
        </div>
    </div>
    @endforeach
@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="featured-product-section">
                <div class="container">
                    <div class="row">
                        <!-- Left side: Banner -->
                        <div class="col-lg-4 mb-4 mb-lg-0 d-flex">
                            <div class="featured-banner flex-grow-1">
                                <img src="{{asset('/')}}public/front/assets/img/slider/banner1.jpg" alt="Men's Premium T-Shirt">
                                <div class="content">
                                    <h4 class="mb-3">MEN'S PREMIUM<br>ACID WASH</h4>
                                    <p class="mb-4">"Itachi Uchiha <br>Retro vibes and faded dreams—this acid wash tee
                                        brings the vintage feels to your closet."</p>
                                    <a href="#" class="btn btn-outline-light">Buy Now</a>
                                </div>
                            </div>
                        </div>
                        <!-- Right side: Product Slider -->
                        <div class="col-lg-8">
                            <h2 class="mb-4 text-center">ALL COLLECTIONS</h2>
                            <div class="product-carousel">
                              @if(isset($randomProducts) && $randomProducts->count() > 0)
    @foreach($randomProducts as $product)
    <div class="product-card card">
        @php
            // Get the first thumbnail image or use a placeholder
             $mobileImage = (is_array($product->thumbnail_image) && count($product->thumbnail_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->thumbnail_image[0]
                            : 'https://placehold.co/800x400';
                        $desktopImage = (is_array($product->main_image) && count($product->main_image) > 0)
                            ? $front_ins_url . 'public/uploads/' . $product->main_image[0]
                            : 'https://placehold.co/800x400';

            // Calculate total stock from all variants to check availability
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
<a href="{{ route('product.show', $product->slug) }}">
        {{-- Picture element for responsive images --}}
    <picture>
        {{-- Desktop and laptop (≥992px) --}}
        <source media="(min-width: 992px)" srcset="{{ $desktopImage }}">
        {{-- Tablet and mobile (<992px) --}}
        <source media="(max-width: 991px)" srcset="{{ $mobileImage }}">
        {{-- Fallback for browsers without <picture> support --}}
        <img src="{{ $mobileImage }}" 
             alt="{{ $product->name }}" 
             class="card-img-top img-fluid">
    </picture>
    </a>
        <div class="product-details-body">
            <h5 class="product-title mb-1">{{ Str::limit($product->name, 25) }}</h5>
            <p class="product-meta mb-1">Category: {{ $product->category->name ?? 'N/A' }}</p>
            <p class="product-meta mb-1">SKU: {{ $product->product_code ?? 'N/A' }}</p>

            @if($totalStock > 0)
                <p class="product-meta text-success fw-bold mb-1"><i class="bi bi-check-circle-fill"></i> In stock</p>
            @else
                <p class="product-meta text-danger fw-bold mb-1"><i class="bi bi-x-circle-fill"></i> Out of stock</p>
            @endif

            <div class="rating-stars mb-2">
                {{-- This can be made dynamic if you add a rating system --}}
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
            </div>

            <p class="price-tag mb-2">
                @if($product->discount_price)
                    <del class="text-muted">৳ {{ number_format($product->base_price, 2) }}</del>
                    <span class="fw-bold">৳ {{ number_format($product->discount_price, 2) }}</span>
                @else
                    <span class="fw-bold">৳ {{ number_format($product->base_price, 2) }}</span>
                @endif
            </p>
            <a href="#" class="btn btn-primary btn-add-cart">Add to Cart</a>
        </div>
    </div>
    @endforeach
@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
@if(isset($offerSectionSetting) && $offerSectionSetting->is_visible && $offerSectionSetting->bundleOffer)
        <section class="section mega-offer-wrapper"  style="background-image: {{ $offerSectionSetting->background_color ?? '#f8f9fa' }} !important;">
            <div class="mega-offer-section">
                <div class="container">
                    <div class="row">
                        <!-- Main banner with two columns -->
                        <div class="col-12">
                            <div class="row g-0 ">
                                <div class="col-lg-6">
                                    <div class="mega-offer-banner" style="background-image: url('{{ $front_ins_url .'public/' .$offerSectionSetting->bundleOffer->image  }}');"></div>
                                </div>
                                <div class="col-lg-6 d-flex align-items-center justify-content-center">
                                    <div class="mega-offer-content">
                                        <h2>{{ $offerSectionSetting->bundleOffer->name ?? 'MEGA BUNDLE OFFER' }}</h2>
                                <p>{{ $offerSectionSetting->bundleOffer->title ?? 'Get upto 50% discount' }}</p>
                                        <div class="countdown-timer-offer" data-end-date="{{ $dealEndDateISO }}">
                                            <div class="timer-box">
                                                <div class="value" id="days-3">{{ $remaining['days'] }}</div>
                                                <div class="label">Days</div>
                                            </div>
                                            <div class="timer-box">
                                                <div class="value" id="hours-3">{{ $remaining['hours'] }}</div>
                                                <div class="label">Hr</div>
                                            </div>
                                            <div class="timer-box">
                                                <div class="value" id="minutes-3">{{ $remaining['minutes'] }}</div>
                                                <div class="label">Min</div>
                                            </div>
                                            <div class="timer-box">
                                                <div class="value" id="seconds-3">{{ $remaining['seconds'] }}</div>
                                                <div class="label">Sec</div>
                                            </div>
                                        </div>
                                        <a href="{{ url($offerSectionSetting->route ?? '#') }}" class="btn btn-go-shopping">Go Shopping</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            
                            $newGlobalCat = \App\Models\BundleOfferProduct::where('bundle_offer_id', $offerSectionSetting->bundleOffer->id)
                            ->get();
                            //dd($newGlobalCat);
                        @endphp
                        <!-- Product Slider Section -->
                        <div class="col-12 product-offer-slider-wrapper">
                            <div class="product-offer-slider">

                                  @forelse ($newGlobalCat as $deal)
            
            @php

            //dd($deal->product_id);
                // Get the first product in the deal to use its image
                $firstProductId = $deal->product_id[0] ?? null;
                //dd($firstProductId);
                $firstProduct = \App\Models\Product::find($firstProductId) ?? null;
//dd($firstProduct);
                // Calculate the original total price by summing up the base prices of all products in the deal
                $originalTotalPrice = 0;
                foreach ($deal->product_id as $productId) {
                    if ($product = \App\Models\Product::find($productId)) {
                        $originalTotalPrice += $product->base_price;
                    }
                }

               // dd($originalTotalPrice);
            @endphp

            <!-- Product Card -->
            <div class="product-card-offer card">
                <img src="{{ $front_ins_url . 'public/uploads/' .$firstProduct->main_image[0] }}" class="card-img-left" alt="{{ $deal->title }}">
                <div class="product-details-offer">
                    <h5 class="product-title-offer">{{ $deal->title }}</h5>
                    <p class="item-price-offer mb-0">
                        {{-- If there's a valid discount price, show both original and discounted price --}}
                        @if ($deal->discount_price > 0 && $deal->discount_price < $originalTotalPrice)
                            <span class="original-price">৳ {{ number_format($originalTotalPrice, 2) }}</span>
                            ৳ {{ number_format($deal->discount_price, 2) }}
                        @else
                            {{-- Otherwise, just show the calculated original price --}}
                            ৳ {{ number_format($originalTotalPrice, 2) }}
                        @endif
                    </p>
                </div>
            </div>
        
        @empty
            <div class="col-12">
                <p class="text-center">No special offers available at the moment.</p>
            </div>
        @endforelse
                                <!-- Product Card 1 -->
                                {{-- <div class="product-card-offer card">
                                    <img src="{{asset('/')}}public/front/assets/img/product/product.webp" class="card-img-left" alt="Product 1">
                                    <div class="product-details-offer">
                                        <h5 class="product-title-offer">2 Drop</h5>
                                        <p class="item-price-offer mb-0">৳ 800.0</p>
                                    </div>
                                </div> --}}
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
@endif
        <section class="featured-category-section">
    <div class="container">
        <div class="category-header">
            <h2>FEATURED CATEGORY</h2>
            <p>Hurry and get coupon with every product</p>
        </div>

        @if(isset($featuredCategories) && $featuredCategories->count() > 0)
        <div class="row category-layout g-3">
            <!-- Left side: Large Banner -->
            <div class="col-lg-6">
                <div class="main-category-banner" style="background-image: url('{{  $front_ins_url .'public/' . $featuredCategories->first()->image}}');">
                    <div class="main-category-banner-content">
                        <h4 class="mb-3">{{ Str::upper($featuredCategories->first()->name) }}</h4>
                        <a href="{{route('animation.category.show', $featuredCategories->first()->slug)}}" class="btn btn-outline-light">Visit Now</a>
                    </div>
                </div>
            </div>

            <!-- Right side: Four Banners -->
            <div class="col-lg-6">
                <div class="row g-3">
                    {{-- Loop through the next four categories --}}
                    @foreach($featuredCategories->skip(1) as $category)
                    <div class="col-6">
                        <div class="sub-category-banner" style="background-image: url('{{ $front_ins_url .'public/' . $category->image }}');">
                            <div class="sub-category-banner-content">
                                <h5>{{ Str::upper($category->name) }}</h5>
                                {{-- You can add a subtitle field to your animation_categories table for this --}}
                                <p class="mb-2">New Collection</p>
                                <a href="{{ route('animation.category.show', $category->slug) }}" class="btn btn-outline-light btn-sm">Visit Now</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
        <section class="why-section">
        <div class="container">
            <div class="why-header">
                <h2>WHY Spotlight Attires STORE?</h2>
            </div>
            <div class="row why-layout">
                <!-- Left side: Banner -->
                <div class="col-lg-5">
                    <div class="left-banner">
                        <div class="left-banner-content">
                            <img src="{{asset('/')}}public/front/assets/img/logo.png">
                        </div>
                    </div>
                </div>

                <!-- Right side: Benefits Grid -->
                <div class="col-lg-7">
                    <div class="row right-benefits-grid">
                        <!-- Benefit 1 -->
                        <div class="col-md-6 mb-4">
                            <div class="benefit-item">
                                <div class="icon-container"><i class="bi bi-person-bounding-box"></i></div>
                                <div class="benefit-item-content">
                                    <h5>UNIQUENESS AND STYLE</h5>
                                    <p>Stand out with exclusive designs that celebrate your individuality.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Benefit 2 -->
                        <div class="col-md-6 mb-4">
                            <div class="benefit-item">
                                <div class="icon-container"><i class="bi bi-tags-fill"></i></div>
                                <div class="benefit-item-content">
                                    <h5>QUALITY AND AFFORDABILITY</h5>
                                    <p>High-quality fashion at prices that won't break the bank.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Benefit 3 -->
                        <div class="col-md-6 mb-4">
                            <div class="benefit-item">
                                <div class="icon-container"><i class="bi bi-toggles2"></i></div>
                                <div class="benefit-item-content">
                                    <h5>VERSATILITY FOR EVERYONE</h5>
                                    <p>From casual to polished, find styles that fit every lifestyle.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Benefit 4 -->
                        <div class="col-md-6 mb-4">
                            <div class="benefit-item">
                                <div class="icon-container"><i class="bi bi-emoji-smile"></i></div>
                                <div class="benefit-item-content">
                                    <h5>CONFIDENCE AND COMFORT</h5>
                                    <p>Feel confident and comfortable with every piece you wear.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Benefit 5 -->
                        <div class="col-md-6 mb-4">
                            <div class="benefit-item">
                                <div class="icon-container"><i class="bi bi-hand-thumbs-up"></i></div>
                                <div class="benefit-item-content">
                                    <h5>CUSTOMER-CENTRIC APPROACH</h5>
                                    <p>Designed with you in mind, offering unmatched experiences.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Benefit 6 -->
                        <div class="col-md-6 mb-4">
                            <div class="benefit-item">
                                <div class="icon-container"><i class="bi bi-lightbulb-fill"></i></div>
                                <div class="benefit-item-content">
                                    <h5>DISCOVER THE UNEXPECTED</h5>
                                    <p>Innovative pieces that refresh your style effortlessly.</p>
                                </div>
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
<script>
    $(function() {
    // Select the timer container div
    const timerContainer = $('.countdown-timer-offer');
    
    // Get the end date string from the data attribute
    const endDateString = timerContainer.data('end-date');
    
    // If there's no date, do nothing
    if (!endDateString) {
        return; 
    }
    
    // Convert the date string into a format JavaScript can use
    const endDate = new Date(endDateString).getTime();

    // Set an interval to run a function every 1000 milliseconds (1 second)
    const timerInterval = setInterval(function() {
        // Get the current time
        const now = new Date().getTime();

        // Calculate the time remaining between now and the end date
        const distance = endDate - now;

        // If the countdown is finished, stop the timer and show a message
        if (distance < 0) {
            clearInterval(timerInterval);
            timerContainer.html("<div style='font-size: 1.5em; color: red;'>Offer has expired!</div>");
            return;
        }
        
        // Calculate the days, hours, minutes, and seconds from the remaining time
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // A simple function to add a leading zero (e.g., 9 becomes "09")
        const pad = (num) => String(num).padStart(2, '0');

        // Use jQuery to find each element by its ID and update its text
        $('#days-3').text(pad(days));
        $('#hours-3').text(pad(hours));
        $('#minutes-3').text(pad(minutes));
        $('#seconds-3').text(pad(seconds));

    }, 1000);
});
</script>
@endsection