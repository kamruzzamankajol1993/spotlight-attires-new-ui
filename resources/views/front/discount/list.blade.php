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

       /* --- NEW CSS FOR STICKY SIDEBAR --- */
    .sticky-filter {
        position: -webkit-sticky; /* For Safari */
        position: sticky;
        top: 100px; /* Adjust this value based on your header's height */
        align-self: flex-start; /* Prevents the column from stretching */
        height: calc(100vh - 100px); /* Sets a max-height for the sidebar */
        overflow-y: auto; /* Adds a scrollbar if the filters are too long */
    }
    /* --- END OF NEW CSS --- */
</style>

@endsection

@section('body')
<main>
    <section class="section">
        <div class="container py-4">

             {{-- <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="fw-bold">Offer Product</h1>
                </div>
            </div> --}}
          
            <div class="row">
                <div class="col-12 d-block d-md-none mb-3">
                    <button class="btn btn-dark w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobile-filter-menu">
                        <i class="bi bi-funnel-fill"></i> Filters
                    </button>
                </div>

                <div class="col-md-3 d-none d-md-block sticky-filter">
                    @include('front.discount.filter_sidebar')
                </div>

                <div class="col-md-9">
                     <!--- new filter section --->
                    <div class="d-flex justify-content-end">
                    <div class="row mb-3">
                        <div class="col">
                            
                                
                            <select class="form-select" id="sort-select-new" aria-label="Sort Products">
                                <option value="default">Sort by</option>
                                <option value="name_asc">A to Z</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="newest">Newest Arrivals</option>
                                <option value="popularity">Most Popular</option>
                            </select>
                            </div>
                        </div>
                    </div>
                    <!--- end new filter section ---->
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
            <div class="offcanvas-body">@include('front.discount.filter_sidebar')</div>
        </div>
    </section>
</main>
 <!-- Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickViewModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="quickViewModalBody">
                <div class="text-center p-5">
                    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Quick View Modal -->
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Use event delegation for buttons in sliders
        $('body').on('click', '.btn-add-cart', function(e) {
            e.preventDefault(); // Prevents the link from jumping to the top of the page

            const productId = $(this).data('product-id');
            const modal = $('#quickViewModal');
            const modalBody = $('#quickViewModalBody');

            // --- START: MODIFIED URL GENERATION ---
            // Create a URL template using the named route and a placeholder
            let urlTemplate = "{{ route('product.quick_view', ['id' => ':id']) }}";
            // Replace the placeholder with the actual product ID
            let productUrl = urlTemplate.replace(':id', productId);
            // --- END: MODIFIED URL GENERATION ---

            // Show the modal
            modal.modal('show');

            // Set a loading state
            modalBody.html('<div class="text-center p-5"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Fetch product details via AJAX
            $.ajax({
                url: productUrl, // Use the dynamically generated URL
                type: 'GET',
                success: function(response) {
                    modalBody.html(response);
                },
                error: function() {
                    modalBody.html('<p class="text-danger text-center">Sorry, we could not load the product details. Please try again.</p>');
                }
            });
        });
    });
</script>
<script>
$(document).ready(function() {
    let page = 2;
    let hasMorePages = {{ $products->hasMorePages() ? 'true' : 'false' }};
    let isLoading = false;
    let currentRequest = null;

    // --- START: MODIFIED PART ---
    // Capture the current category slug from the controller
    const EXTRA_CATEGORY_SLUG = '{{ $extraCategory->slug ?? '' }}';

    function getFilters() {
        const selectedSizes = $('.size-filter:checked').map(function() {
            return $(this).val();
        }).get();

        return {
            // Add the slug to the data sent in the AJAX request
            extra_category_slug: EXTRA_CATEGORY_SLUG, 
            min_price: $('#min-price-slider').val(),
            max_price: $('#max-price-slider').val(),
            stock_status: $('input[name="stock-status"]:checked').val(),
            sort_by: $('#sort-select-new').val(),
            sizes: selectedSizes 
        };
    }
    // --- END: MODIFIED PART ---

    function loadProducts(reset = false) {
    if (isLoading) return;
    if (reset) {
        page = 1;
        $('#product-list').html('');
    }

    isLoading = true;
    $('#loading-spinner').show();
    if (currentRequest) currentRequest.abort();

    currentRequest = $.ajax({
        // --- START: MODIFIED LINE ---
        // Use the new, dedicated route for discount filtering
        url: `{{ route('discount.ajax_filter') }}?page=${page}`,
        // --- END: MODIFIED LINE ---
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

    $('#sort-select-new').on('change', function() {
        loadProducts(true); // Reset and load products with the new sorting
    });
    // Listeners for Price and Stock
    $('#price-filter-btn').on('click', () => loadProducts(true));
    $('.stock-status-filter').on('change', () => loadProducts(true));
    $('#min-price-slider, #max-price-slider').on('input', function() {
        let min = parseInt($('#min-price-slider').val()), max = parseInt($('#max-price-slider').val());
        if (min > max) [min, max] = [max, min];
        $('#price-range-display').text(`Price: ৳${min} - ৳${max}`);
    });

    // --- NEW: Listener for size checkboxes ---
    $(document).on('change', '.size-filter', function() {
        loadProducts(true);
    });
});
</script>
@endsection