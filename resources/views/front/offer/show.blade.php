@extends('front.master.master')

@section('title', $offer->name ?? 'Special Offer')

@section('css')
<style>
    #loading-spinner { 
        display: none; 
        text-align: center; 
        padding: 20px 0; 
    }
    .offer-filter.active { 
        font-weight: bold; 
        color: #0d6efd !important; 
    }
</style>
@endsection

@section('body')
<main>
    <section class="section">
        <div class="container py-4">
            <h2 class="text-center mb-4">{{ $offer->title }}</h2>
            <div class="row">
                <div class="col-12 d-block d-md-none mb-3">
                    <button class="btn btn-dark w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobile-filter-menu">
                        <i class="bi bi-tags-fill"></i> View Other Offers
                    </button>
                </div>

                <div class="col-md-3 d-none d-md-block sticky-filter">
                    @include('front.offer.filter_sidebar')
                </div>

                <div class="col-md-9">
                    <div class="product-grid">
                        <div id="product-list" class="row row-cols-2 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3"
                             data-base-offer-id="{{ $offer->id }}">
                            
                            @include('front.offer.bundle_card_partial', [
                                'bundleDeals' => $bundleDeals, 
                                'productsCollection' => $productsCollection
                            ])

                        </div>
                    </div>
                    <div id="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobile-filter-menu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Other Offers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                @include('front.offer.filter_sidebar')
            </div>
        </div>
    </section>
</main>
@endsection

@section('script')
@section('script')
<script>
$(document).ready(function() {
    let page = 2;
    let hasMorePages = {{ $bundleDeals->hasMorePages() ? 'true' : 'false' }};
    let isLoading = false;
    let currentRequest = null;

    // MODIFIED: getFilters now includes price range
    function getFilters() {
        let filters = {
            min_price: $('#min-price-slider').val(),
            max_price: $('#max-price-slider').val()
        };
        const activeOffer = $('.offer-filter.active').data('id');

        if (activeOffer) {
            filters.offer_id = activeOffer;
        } else {
            filters.offer_id = $('#product-list').data('base-offer-id');
        }
        return filters;
    }

    function loadProducts(reset = false) {
        if (isLoading) return;
        if (reset) {
            page = 1;
            $('#product-list').html('');
            $('html, body').animate({ scrollTop: $('.product-grid').offset().top - 80 }, 300);
        }

        isLoading = true;
        $('#loading-spinner').show();
        if (currentRequest) currentRequest.abort();

        currentRequest = $.ajax({
            url: `{{ route('offer.filter') }}?page=${page}`,
            type: 'GET',
            data: getFilters(),
            success: function(response) {
                if (reset) {
                    $('#product-list').html(response.html);
                } else {
                    $('#product-list').append(response.html);
                }
                hasMorePages = response.hasMorePages;
                page++;
                if (!hasMorePages) $('#loading-spinner').hide();
            },
            error: function(xhr, status, error) {
                if (status !== 'abort') console.error("Error:", error);
            },
            complete: function() {
                isLoading = false;
                if (hasMorePages) $('#loading-spinner').hide();
            }
        });
    }

    // On-scroll loader (no changes here)
    $(window).scroll(function() {
        if ($('#product-list').length) {
            const listBottom = $('#product-list').offset().top + $('#product-list').height();
            const screenBottom = $(window).scrollTop() + $(window).height();
            if (screenBottom >= listBottom - 500) {
                if (hasMorePages && !isLoading) {
                    loadProducts();
                }
            }
        }
    });

    // Event listener for the offer filters (no changes here)
    $(document).on('click', '.offer-filter', function(e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.offer-filter').removeClass('active');
            $(this).addClass('active');
        }
        loadProducts(true);
    });

    // --- ADD THIS NEW BLOCK FOR PRICE FILTER ---
    // Trigger filter when the price filter button is clicked
    $('#price-filter-btn').on('click', function() {
        loadProducts(true);
    });

    // Update the price range text as the sliders are moved
    $('#min-price-slider, #max-price-slider').on('input', function() {
        let minPrice = parseInt($('#min-price-slider').val());
        let maxPrice = parseInt($('#max-price-slider').val());
        if (minPrice > maxPrice) {
            // Swap values if min crosses over max
            [minPrice, maxPrice] = [maxPrice, minPrice];
        }
        $('#price-range-display').text(`Price: ৳${minPrice} - ৳${maxPrice}`);
    });
    // --- END OF NEW BLOCK ---
});
</script>
@endsection
@endsection