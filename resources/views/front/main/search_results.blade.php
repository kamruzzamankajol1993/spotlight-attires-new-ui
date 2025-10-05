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
      /* Container for the product images */
    .product-image-container {
        position: relative; /* Needed to position the hover image correctly */
        display: block;
        overflow: hidden; /* Ensures images stay within the card boundaries */
    }

    /* Styling for both default and hover images */
    .product-image-container picture img {
        transition: transform 0.3s ease-in-out; /* Optional: adds a slight zoom effect on hover */
    }

    /* The hover image is positioned directly on top of the default one */
    .product-image-hover {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0; /* It's completely invisible by default */
        transition: opacity 0.3s ease-in-out; /* This creates the smooth fade effect */
    }

    /* When you hover over the container... */
    .product-image-container:hover .product-image-hover {
        opacity: 1; /* ...the hover image fades in and becomes visible */
    }
    
    /* Optional: Slight zoom effect on the image when hovering */
    .product-image-container:hover picture img {
        transform: scale(1.05);
    }
    /*new css for in line*/
    #active-filters-container {
    display: none; /* Hidden by default */
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #eee;
}
.filter-tag {
    display: inline-flex;
    align-items: center;
    background-color: #e9ecef;
    border: 1px solid #dee2e6;
    border-radius: 1rem;
    padding: 0.25rem 0.75rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}
.remove-filter {
    margin-left: 0.5rem;
    cursor: pointer;
    font-weight: bold;
}
#clear-all-filters {
    font-size: 0.875rem;
    font-weight: bold;
    color: #dc3545;
    text-decoration: none;
}
#clear-all-filters:hover {
    text-decoration: underline;
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
                    <div id="active-filters-container">
        <div id="active-filters-list" class="d-inline">
            </div>
        <a href="#" id="clear-all-filters" class="ms-2">Clear All</a>
    </div>
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
    const searchQuery = "{{ $searchQuery ?? '' }}";

    /**
     * NEW: This function reads all active filters from the sidebar
     * and generates the visual tags above the product grid.
     */
    function updateActiveFiltersDisplay() {
        const filtersList = $('#active-filters-list');
        const filtersContainer = $('#active-filters-container');
        filtersList.html(''); // Clear existing tags

        let hasActiveFilters = false;

        // 1. Check for Category/Subcategory/Animation filters
        $('.main-category-filter.active, .subcategory-filter.active, .animation-category-filter.active').each(function() {
            const text = $(this).text();
            const id = $(this).data('id');
            const type = $(this).hasClass('main-category-filter') ? 'category' :
                         $(this).hasClass('subcategory-filter') ? 'subcategory' : 'animation';
            
            filtersList.append(
                `<span class="filter-tag" data-filter-type="${type}" data-filter-value="${id}">${text} <span class="remove-filter" title="Remove filter">&times;</span></span>`
            );
            hasActiveFilters = true;
        });

        // 2. Check for Price filter
        const minPrice = $('#min-price-slider').val();
        const maxPrice = $('#max-price-slider').val();
        if (minPrice > 0 || maxPrice < 10000) {
            filtersList.append(
                `<span class="filter-tag" data-filter-type="price">Price: ৳ ${minPrice} - ৳ ${maxPrice} <span class="remove-filter" title="Remove filter">&times;</span></span>`
            );
            hasActiveFilters = true;
        }

        // 3. Check for Stock Status filter
        const stockStatus = $('input[name="stock-status"]:checked');
        if (stockStatus.val() !== "") {
            const text = stockStatus.next('label').text();
            filtersList.append(
                `<span class="filter-tag" data-filter-type="stock">${text} <span class="remove-filter" title="Remove filter">&times;</span></span>`
            );
            hasActiveFilters = true;
        }

        // 4. Check for Size filters
        $('.size-filter:checked').each(function() {
            const size = $(this).val();
            filtersList.append(
                `<span class="filter-tag" data-filter-type="size" data-filter-value="${size}">${size} <span class="remove-filter" title="Remove filter">&times;</span></span>`
            );
            hasActiveFilters = true;
        });

         // --- START: NEW CODE FOR SORTING TAG ---
        const sortSelect = $('#sort-select-new');
        const sortValue = sortSelect.val();
        if (sortValue && sortValue !== 'default') {
            const sortText = sortSelect.find('option:selected').text();
            filtersList.append(
                `<span class="filter-tag" data-filter-type="sort">Sort by: ${sortText} <span class="remove-filter" title="Remove filter">&times;</span></span>`
            );
            hasActiveFilters = true;
        }
        // --- END: NEW CODE FOR SORTING TAG ---

        // Show or hide the entire container based on filter activity
        if (hasActiveFilters) {
            filtersContainer.slideDown(200);
        } else {
            filtersContainer.slideUp(200);
        }
    }

    function getFilters() {
        const selectedSizes = $('.size-filter:checked').map(function() { return $(this).val(); }).get();
        return {
            query: searchQuery,
            category_id: $('.main-category-filter.active').data('id'),
            subcategory_id: $('.subcategory-filter.active').data('id'),
            animation_category_id: $('.animation-category-filter.active').data('id'),
            min_price: $('#min-price-slider').val(),
            max_price: $('#max-price-slider').val(),
            stock_status: $('input[name="stock-status"]:checked').val(),
            sort_by: $('#sort-select-new').val(),
            sizes: selectedSizes 
        };
    }

    function loadProducts(reset = false) {
        if (isLoading) return;
        if (reset) {
            page = 1;
            $('#product-list').html('');
        }
        
        updateActiveFiltersDisplay(); // Call this function to update tags every time we load products

        isLoading = true;
        $('#loading-spinner').show();
        if (currentRequest) currentRequest.abort();

        currentRequest = $.ajax({
            url: `{{ route('products.ajax_search_filter') }}?page=${page}`,
            type: 'GET',
            data: getFilters(),
            success: function(response) {
                if (reset) $('#product-list').html(response.html);
                else $('#product-list').append(response.html);
                hasMorePages = response.hasMorePages;
                page++;
                if (!hasMorePages) $('#loading-spinner').hide();
            },
            error: function(xhr, status, error) { if (status !== 'abort') console.error("Error:", error); },
            complete: function() { isLoading = false; if (hasMorePages) $('#loading-spinner').hide(); }
        });
    }
    
    // Initial call to set tags if the page loads with filters already applied (e.g., from browser back button)
    updateActiveFiltersDisplay();

    /* --- EVENT LISTENERS --- */

    // Sidebar filter changes
    function applyFilterChange() { loadProducts(true); }
    $('.main-category-filter, .subcategory-filter, .animation-category-filter').on('click', function(e) { e.preventDefault(); $(this).toggleClass('active'); applyFilterChange(); });
    $('#sort-select-new, .stock-status-filter, .size-filter').on('change', applyFilterChange);
    $('#price-filter-btn').on('click', applyFilterChange);
    $('#min-price-slider, #max-price-slider').on('input', function() { let min = parseInt($('#min-price-slider').val()), max = parseInt($('#max-price-slider').val()); if (min > max) [min, max] = [max, min]; $('#price-range-display').text(`Price: ৳ ${min} - ৳ ${max}`); });

    // NEW: Event listener for removing a single filter tag
    $(document).on('click', '.remove-filter', function() {
        const tag = $(this).closest('.filter-tag');
        const type = tag.data('filter-type');
        const value = tag.data('filter-value');

        switch(type) {
            case 'category':
            case 'subcategory':
            case 'animation':
                $(`a[data-id="${value}"]`).removeClass('active');
                break;
            case 'price':
                $('#min-price-slider').val(0);
                $('#max-price-slider').val(10000);
                $('#price-range-display').text(`Price: ৳ 0 - ৳ 10000`);
                break;
            case 'stock':
                $('#all-stock').prop('checked', true);
                break;
            case 'size':
                $(`.size-filter[value="${value}"]`).prop('checked', false);
                break;
             // --- START: NEW CASE FOR SORTING ---
            case 'sort':
                $('#sort-select-new').val('default');
                break;
            // --- END: NEW CASE FOR SORTING ---
        }
        loadProducts(true); // Reload products after removing the filter
    });

    // NEW: Event listener for the "Clear All" button
    $('#clear-all-filters').on('click', function(e) {
        e.preventDefault();
        // Reset all inputs in the sidebar
        $('.main-category-filter, .subcategory-filter, .animation-category-filter').removeClass('active');
        $('#min-price-slider').val(0);
        $('#max-price-slider').val(10000);
        $('#price-range-display').text(`Price: ৳ 0 - ৳ 10000`);
        $('#all-stock').prop('checked', true);
        $('.size-filter').prop('checked', false);
        $('#sort-select-new').val('default');

        loadProducts(true); // Reload products
    });
    
    // On-scroll loader
    $(window).scroll(function() { if ($('#product-list').length > 0 && ($(window).scrollTop() + $(window).height() >= $('#product-list').offset().top + $('#product-list').height() - 500)) { if (hasMorePages && !isLoading) loadProducts(); } });
});
</script>
@endsection