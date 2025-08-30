@extends('front.master.master')

@section('title', $category->name ?? 'Category')

@section('css')
<style>
    /* Add some basic styling for the loading spinner and active filter */
    #loading-spinner {
        display: none;
        text-align: center;
        padding: 20px 0;
    }
    .subcategory-filter.active {
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
                    <button class="btn btn-dark w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobile-filter-menu" aria-controls="mobile-filter-menu">
                        <i class="bi bi-funnel-fill"></i> Filter Products
                    </button>
                </div>

                <div class="col-md-3 d-none d-md-block sticky-filter" id="desktop-filter">
                    @include('front.category.filter_sidebar')
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
                        {{-- This container will be updated by AJAX --}}
                        <div id="product-list" class="row row-cols-2 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3" data-base-category-id="{{ $category->id }}">
                            {{-- Load the initial products --}}
                            @include('front.category.product_card_partial', ['products' => $products])
                        </div>
                    </div>
                    {{-- Loading spinner for infinite scroll --}}
                    <div id="loading-spinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobile-filter-menu" aria-labelledby="mobile-filter-menu-label">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobile-filter-menu-label">Filter Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                @include('front.category.filter_sidebar')
            </div>
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

    /**
     * MODIFIED: This function now checks for active filters first.
     * If none are active, it falls back to the initial category of the page.
     */
    function getFilters() {
        let filters = {
            min_price: $('#min-price-slider').val(),
            max_price: $('#max-price-slider').val(),
            stock_status: $('input[name="stock-status"]:checked').val(),
             sort_by: $('#sort-select-new').val() 
        };

        const activeMainCategory = $('.main-category-filter.active').data('id');
        const activeSubcategory = $('.subcategory-filter.active').data('id');

        // Prioritize a manually selected filter
        if (activeMainCategory) {
            filters.category_id = activeMainCategory;
        } else if (activeSubcategory) {
            filters.subcategory_id = activeSubcategory;
        } else {
            // If no filter is active, use the base context of the page for on-scroll loading
            const baseCategoryId = $('#product-list').data('base-category-id');
            if (baseCategoryId) {
                filters.category_id = baseCategoryId;
            }
        }

        return filters;
    }

    function loadProducts(reset = false) {
        if (isLoading) return;
         if (reset) {
        page = 1;
        $('#product-list').html(''); // Clears the current products

        // ADD THIS LINE TO SCROLL UP
        $('html, body').animate({ scrollTop: $('.product-grid').offset().top - 80 }, 300);
    }

        isLoading = true;
        $('#loading-spinner').show();
        if (currentRequest) currentRequest.abort();

        currentRequest = $.ajax({
            url: `{{ route('products.filter') }}?page=${page}`,
            type: 'GET',
            data: getFilters(), // This now sends the correct context
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

    // Scroll listener - no changes needed here
    $(window).scroll(function() {
        if ($('#product-list').length) {
            const productListBottom = $('#product-list').offset().top + $('#product-list').height();
            const screenBottom = $(window).scrollTop() + $(window).height();
            if (screenBottom >= productListBottom - 500) {
                if (hasMorePages && !isLoading) {
                    loadProducts();
                }
            }
        }
    });

     $('#sort-select-new').on('change', function() {
        loadProducts(true); // Reset and load products with the new sorting
    });
    
    // Filter event listeners - no changes needed here
    $('#price-filter-btn').on('click', function() { loadProducts(true); });
    $(document).on('change', '.stock-status-filter', function() { loadProducts(true); });
    $(document).on('click', '.main-category-filter', function(e) {
        e.preventDefault();
        $('.subcategory-filter').removeClass('active');
        if ($(this).hasClass('active')) {
             $(this).removeClass('active');
        } else {
             $('.main-category-filter').removeClass('active');
             $(this).addClass('active');
        }
        loadProducts(true);
    });
    $(document).on('click', '.subcategory-filter', function(e) {
        e.preventDefault();
        $('.main-category-filter').removeClass('active');
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.subcategory-filter').removeClass('active');
            $(this).addClass('active');
        }
        loadProducts(true);
    });
    $('#min-price-slider, #max-price-slider').on('input', function() {
        let minPrice = parseInt($('#min-price-slider').val());
        let maxPrice = parseInt($('#max-price-slider').val());
        if (minPrice > maxPrice) [minPrice, maxPrice] = [maxPrice, minPrice];
        $('#price-range-display').text(`Price: ৳${minPrice} - ৳${maxPrice}`);
    });
});
</script>
@endsection