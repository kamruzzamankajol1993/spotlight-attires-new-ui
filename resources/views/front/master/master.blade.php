<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M9XNZMSC');</script>
<!-- End Google Tag Manager -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>@yield('title')</title>
    <meta name="description" content="{{$front_ins_name}}">
    <meta name="keywords" content="{{$front_ins_name}}">
    <meta name="author" content="{{$front_ins_name}}">
    <link rel="canonical" href="{{url()->current()}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{url()->current()}}">
    <meta property="og:title" content="{{$front_ins_name}}">
    <meta property="og:description" content="{{$front_ins_name}}">
    <meta property="og:image" content="{{$front_ins_url}}{{$front_icon_name}}">

<!-- Favicon -->
    <link rel="shortcut icon" href="{{$front_ins_url}}{{ $front_icon_name }}">
    <!-- Vendor CSS Files -->
    <link href="{{asset('/')}}public/front/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{asset('/')}}public/front/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{asset('/')}}public/front/assets/vendor/aos/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/')}}public/front/assets/vendor/fontawesome-free/css/fontawesome.css">
    <link rel="stylesheet" href="{{asset('/')}}public/front/assets/vendor/slickslider/slick-theme.css">
    <link rel="stylesheet" href="{{asset('/')}}public/front/assets/vendor/slickslider/slick.css">

    <!-- Main CSS File -->
    <link href="{{asset('/')}}public/front/assets/css/main.css?v={{ filemtime(public_path('front/assets/css/main.css')) }}" rel="stylesheet">
    @yield('css')
    <script src="{{asset('/')}}public/front/assets/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');

fbq('init', '1204087944905871'); 
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1204087944905871&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
<style>
    /* মোডাল ওপেন থাকলেও বডি ডানে সরবে না */
    body.modal-open {
        padding-right: 0 !important;
        overflow: hidden;
    }

    /* হেডার যদি ফিক্সড থাকে তবে সেটির পজিশন ঠিক রাখা */
    body.modal-open .header, 
    body.modal-open .fixed-top,
    body.modal-open header {
        padding-right: 0 !important;
    }

    /* মোবাইলে মোডালের মার্জিন ঠিক করা */
    @media (max-width: 576px) {
        .modal-dialog {
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100% - 20px);
        }
        
        #eidNoticeModal .modal-content {
            width: 90%; /* স্ক্রিনের দুই পাশে সামান্য গ্যাপ রাখবে */
            border-radius: 15px;
        }
    }
</style>
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M9XNZMSC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    @include('front.include.header')

    @yield('body')

    @include('front.include.footer')

    <!-- Vendor JS Files -->
  <div class="modal fade" id="eidNoticeModal" tabindex="-1" aria-labelledby="eidNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;"> 
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2 bg-white rounded-circle p-2 shadow-sm" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
            
            <div class="modal-body p-0 text-center">
                <div id="imageLoading" class="w-100 d-flex flex-column justify-content-center align-items-center bg-white p-4" style="min-height: 300px;">
                    <div class="spinner-border text-dark mb-4" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <h4 class="fw-bold text-dark mb-3">শুভ নববর্ষ!</h4>
                    <p class="text-dark" style="font-size: 18px;">পহেলা বৈশাখ উপলক্ষে ১৪% বিশেষ ছাড়!</p>
                </div>
                
                <img id="noticeImage" src="{{asset('/')}}public/coupon.jpeg" alt="Boishakh Offer" class="img-fluid w-100 d-block d-none">
            </div>
            
            <div class="modal-footer justify-content-center border-0 pb-3 pt-0 bg-white">
                <a href="https://spotlightattires.com/shop" class="btn btn-dark px-5 py-2 rounded-pill" data-bs-dismiss="modal">শপিং চালিয়ে যান</a>
            </div>
        </div>
    </div>
</div>
    

    
    <script src="{{asset('/')}}public/front/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('/')}}public/front/assets/vendor/aos/aos.js"></script>
    <script src="{{asset('/')}}public/front/assets/vendor/slickslider/slick.min.js"></script>

    <!-- Main JS File -->
    <script src="{{asset('/')}}public/front/assets/js/main.js"></script>
    @yield('script')
 <script>
    $(document).ready(function() {
        // একব দেখালে এই সশনে আর দেখাে না
        if (!sessionStorage.getItem('eidNoticeShown')) {
            
            // পেজ লোড হওার ১ সেকেন্ র মোডাল ওপে হবে
            setTimeout(function() {
                $('#eidNoticeModal').modal('show');
                
                var $img = $('#noticeImage');
                var $loading = $('#imageLoading');

                // ব্রউজারে আগ থকেই ইমেজ ক্াশ করা থাকল
                if ($img[0].complete && $img[0].naturalWidth > 0) {
                    $loading.addClass('d-none');
                    $img.removeClass('d-none');
                } 
                // ইমেজ লোড হওয়ার অপেক্ষায় থাকল
                else {
                    $img.on('load', function() {
                        $loading.addClass('d-none');
                        $img.removeClass('d-none');
                    }).on('error', function() {
                        // কোনো কারণ ইেজ লোড না হল লোডারটি হাড হয়ে শুধু টেক্সটটিই থেে যাবে
                        $loading.find('.spinner-border').addClass('d-none');
                    });
                }
                
                sessionStorage.setItem('eidNoticeShown', 'true');
            }, 100); 
            
        }
    });
 </script>
    <script>
    $(document).ready(function() {
        $('.main-slider').slick({
            dots: true,
            arrows: true,
            infinite: true,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('.product-slider').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [{
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        $('.product-carousel').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [{
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('.product-offer-slider').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [{
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });

        // Countdown Timer Logic
        
        //setInterval(updateCountdown, 1000);
    });
    </script>

    <!-- cart script--->
    <script>
    // Global function to update the cart offcanvas display
    function updateCartOffcanvas() {
        $.ajax({
            url: '{{ route("cart.content") }}',
            type: 'GET',
            success: function(response) {
                $('.cart-products').html(response.html);
                $('#cart-subtotal').text(' ' + response.subtotal);
                $('#mobile-cart-count').text(response.count);
                $('#desktop-cart-count').text(response.count);
                $(document.body).trigger('cart-updated');
            },
            error: function() {
                $('.cart-products').html('<p class="text-danger text-center p-3">Could not load cart. Please try again.</p>');
            }
        });
    }

    $(document).ready(function() {
        // Load initial cart content when the page loads
        updateCartOffcanvas();

        // Use event delegation for removing items from the dynamically loaded cart
        $('body').on('click', '.remove-cart-item', function() {
            const rowId = $(this).data('row-id');
            const cartItemDiv = $(this).closest('.cart-product-item');

            $.ajax({
                url: '{{ route("cart.remove") }}',
                type: 'POST',
                data: {
                    rowId: rowId,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    cartItemDiv.css('opacity', '0.5'); // Visual feedback
                },
                success: function(response) {
                    // The 'remove' route now returns the full updated cart content, so we just re-render everything
                    $('.cart-products').html(response.html);
                    $('#cart-subtotal').text('৳ ' + response.subtotal);
                    $('#mobile-cart-count').text(response.count);
                    $('#desktop-cart-count').text(response.count);

                    $(document.body).trigger('cart-updated');
                },
                error: function() {
                     Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Error removing item. Please try again.'
                    });
                    cartItemDiv.css('opacity', '1'); // Revert visual feedback on error
                }
            });
        });

        // Use event delegation for updating item quantity
        $('body').on('click', '.cart-quantity-btn', function() {
            const rowId = $(this).data('row-id');
            const change = parseInt($(this).data('change'));
            const quantitySpan = $(this).parent().find('.cart-quantity-value');
            let currentQuantity = parseInt(quantitySpan.text());
            let newQuantity = currentQuantity + change;

            if (newQuantity < 1) {
                return; // Quantity cannot be less than 1
            }

            $.ajax({
                url: '{{ route("cart.update") }}',
                type: 'POST',
                data: {
                    rowId: rowId,
                    quantity: newQuantity,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // The 'update' route also returns the full cart content
                    $('.cart-products').html(response.html);
                    $('#cart-subtotal').text(' ' + response.subtotal);
                    $('#mobile-cart-count').text(response.count);
                    $('#desktop-cart-count').text(response.count);

                    $(document.body).trigger('cart-updated');
                },
                error: function() {
                    Swal.fire({
                      icon: 'error',
                      title: 'Update Failed',
                      text: 'Error updating quantity. Please try again.'
                    });
                }
            });
        });
    });
</script>

<script>
$(document).ready(function() {
    function initializeAjaxSearch(inputSelector, resultsSelector, iconSelector) {
        let searchTimeout;
        const searchInput = $(inputSelector);
        const resultsContainer = $(resultsSelector);
        const searchIcon = $(iconSelector);

        // Function to perform the redirect
        function goToSearchPage() {
            const query = searchInput.val().trim();
            if (query) {
                window.location.href = `{{ route('products.search') }}?query=${encodeURIComponent(query)}`;
            }
        }

        searchInput.on('keyup', function(e) {
            if (e.key === 'Enter') {
                goToSearchPage();
                return;
            }
            
            clearTimeout(searchTimeout);
            const query = $(this).val().trim();

            if (query.length < 1) {
                resultsContainer.hide().html('');
                return;
            }

            searchTimeout = setTimeout(function() {
                resultsContainer.show().html('<div class="text-center p-3"><span class="spinner-border spinner-border-sm"></span></div>');
                $.ajax({
                    url: '{{ route("products.ajax_search") }}',
                    method: 'GET',
                    data: { query: query },
                    success: function(products) {
                        resultsContainer.html('');
                        if (products && products.length > 0) {
                            products.forEach(function(product) {
                                let priceHtml = product.discount_price > 0 ? `<span class="fw-bold text-dark">৳ ${product.discount_price}</span> <del class="text-muted small ms-2"> ${product.base_price}</del>` : `<span class="fw-bold text-dark">৳ ${product.base_price}</span>`;
                                const productHtml = `<a href="${product.url}" class="search-result-item"><img src="${product.image_url}" alt="${product.name}"><div class="search-result-info"><div class="fw-bold">${product.name}</div><div class="price">${priceHtml}</div></div></a>`;
                                resultsContainer.append(productHtml);
                            });
                        } else {
                            resultsContainer.html('<div class="text-center p-3 text-muted">No products found.</div>');
                        }
                    },
                    error: function() {
                        resultsContainer.html('<div class="text-center p-3 text-danger">Search failed.</div>');
                    }
                });
            }, 300);
        });
        
        searchIcon.on('click', goToSearchPage);
    }

    // Initialize the search for both desktop and mobile inputs
    initializeAjaxSearch('#product-search-input', '#search-results-container', '#desktop-search-icon');
    initializeAjaxSearch('#mobile-product-search-input', '#mobile-search-results-container', '#mobile-search-icon');

    // Hide search results when clicking anywhere else on the page
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('.search-results-popup').hide();
        }
    });
});
</script>
<script>
// ১. গ্লোবাল ্র্াকিং ফাশন (FB & GTM)
function fb_track_add_to_cart(id, name, price) {
    try {
        var cleanPrice = parseFloat(price) || 0;
        var pId = id ? id.toString() : '';

        // --- Meta Pixel (Facebook) ---
        if (typeof fbq === 'function') {
            fbq('track', 'AddToCart', {
                content_ids: [pId],
                content_name: name,
                content_type: 'product',
                value: cleanPrice,
                currency: 'BDT'
            });
        }

        // --- Google Tag Manager (DataLayer) ---
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'add_to_cart',
            'ecommerce': {
                'currency': 'BDT',
                'value': cleanPrice,
                'items': [{
                    'item_id': pId,
                    'item_name': name,
                    'price': cleanPrice,
                    'quantity': 1
                }]
            }
        });

        console.log("Tracking Success (FB & GTM): " + name);
    } catch (e) {
        console.error("Tracking Error:", e);
    }
}

// ২. গ্োবাল ্লিক ্যান্ডলার (ব পেজর াটের জন্য)
$(document).on('click', '.btn-add-cart', function(e) {
    e.preventDefault(); // পেজ ন পরে লাফ না দেয়

    // closest বহার করা হয়ে যাতে ভেতে আইকনে ক্িক করলেও মেন বাটন েকে ডাটা পা
    var btn = $(this).closest('.btn-add-cart');
    
    var id = btn.attr('data-product-id') || btn.data('product-id');
    var name = btn.attr('data-product-name') || btn.data('product-name');
    var price = btn.attr('data-product-price') || btn.data('product-price');

    console.log(id);
    console.log(name);
    console.log(price);

    if (id) {
        fb_track_add_to_cart(id, name, price);
    } else {
        console.error("Tracking failed: Data attributes not found on the clicked element.");
    }
});
</script>
</body>

</html>