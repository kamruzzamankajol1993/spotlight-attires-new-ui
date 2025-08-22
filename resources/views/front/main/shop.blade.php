@extends('front.master.master')

@section('title', 'Shop All Products')

@section('css')
<style>
    #loading-spinner { display: none; text-align: center; padding: 20px 0; }
    .main-category-filter.active,
    .subcategory-filter.active,
    .animation-category-filter.active { 
        font-weight: bold; 
        color: #0d6efd !important; 
    }
</style>
@endsection

@section('body')
<main>
    <section class="section">
        <div class="container py-4">
          
            <div class="row">
                <div class="col-12 d-block d-md-none mb-3">
                    <button class="btn btn-dark w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobile-filter-menu">
                        <i class="bi bi-funnel-fill"></i> Filters
                    </button>
                </div>

                <div class="col-md-3 d-none d-md-block sticky-filter">
                    @include('front.main.filter_sidebar')
                </div>

                <div class="col-md-9">
                    <div class="product-grid">
                        <div id="product-list" class="row row-cols-2 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3">
                            @include('front.category.product_card_partial', ['products' => $products])
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
            <div class="offcanvas-header"><h5 class="offcanvas-title">Filters</h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
            <div class="offcanvas-body">@include('front.main.filter_sidebar')</div>
        </div>
    </section>
</main>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let page = 2;
    let hasMorePages = {{ $products->hasMorePages() ? 'true' : 'false' }};
    let isLoading = false;
    let currentRequest = null;

    function getFilters() {
        return {
            category_id: $('.main-category-filter.active').data('id'),
            subcategory_id: $('.subcategory-filter.active').data('id'),
            animation_category_id: $('.animation-category-filter.active').data('id'),
            min_price: $('#min-price-slider').val(),
            max_price: $('#max-price-slider').val(),
            stock_status: $('input[name="stock-status"]:checked').val()
        };
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
            // Use the new, dedicated route
            url: `{{ route('shop.ajax_filter') }}?page=${page}`,
            type: 'GET',
            data: getFilters(),
            success: function(response) {
                if (reset) $('#product-list').html(response.html);
                else $('#product-list').append(response.html);
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

    // On-scroll loader
    $(window).scroll(function() {
        if ($('#product-list').length > 0 && ($(window).scrollTop() + $(window).height() >= $('#product-list').offset().top + $('#product-list').height() - 500)) {
            if (hasMorePages && !isLoading) loadProducts();
        }
    });

    // --- Event Listeners ---
    function handleCategoryClick(selector, otherSelectors) {
        $(document).on('click', selector, function(e) {
            e.preventDefault();
            otherSelectors.forEach(sel => $(sel).removeClass('active'));
            const $el = $(this);
            if ($el.hasClass('active')) $el.removeClass('active');
            else {
                $(selector).removeClass('active');
                $el.addClass('active');
            }
            loadProducts(true);
        });
    }

    handleCategoryClick('.main-category-filter', ['.subcategory-filter', '.animation-category-filter']);
    handleCategoryClick('.subcategory-filter', ['.main-category-filter', '.animation-category-filter']);
    handleCategoryClick('.animation-category-filter', ['.main-category-filter', '.subcategory-filter']);

    // Listeners for Price and Stock
    $('#price-filter-btn').on('click', () => loadProducts(true));
    $('.stock-status-filter').on('change', () => loadProducts(true));
    $('#min-price-slider, #max-price-slider').on('input', function() {
        let min = parseInt($('#min-price-slider').val()), max = parseInt($('#max-price-slider').val());
        if (min > max) [min, max] = [max, min];
        $('#price-range-display').text(`Price: ৳${min} - ৳${max}`);
    });
});
</script>
@endsection