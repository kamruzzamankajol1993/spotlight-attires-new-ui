<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>@yield('title')</title>
    <meta name="description" content="{{$front_ins_name}}">
    <meta name="keywords" content="{{$front_ins_name}}">
    <meta name="author" content="{{$front_ins_name}}">
    <link rel="canonical" href="{{url()->current()}}">

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
    <link href="{{asset('/')}}public/front/assets/css/main.css" rel="stylesheet">
    @yield('css')
</head>

<body>
    @include('front.include.header')

    @yield('body')

    @include('front.include.footer')

    <!-- Vendor JS Files -->
    <script src="{{asset('/')}}public/front/assets/js/jquery.min.js"></script>
    <script src="{{asset('/')}}public/front/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('/')}}public/front/assets/vendor/aos/aos.js"></script>
    <script src="{{asset('/')}}public/front/assets/vendor/slickslider/slick.min.js"></script>

    <!-- Main JS File -->
    <script src="{{asset('/')}}public/front/assets/js/main.js"></script>
    @yield('script')

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
                        slidesToShow: 2,
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
                $('#cart-subtotal').text('৳ ' + response.subtotal);
                $('#mobile-cart-count').text(response.count);
                $('#desktop-cart-count').text(response.count);
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
                },
                error: function() {
                    alert('Error removing item. Please try again.');
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
                    $('#cart-subtotal').text('৳ ' + response.subtotal);
                    $('#mobile-cart-count').text(response.count);
                    $('#desktop-cart-count').text(response.count);
                },
                error: function() {
                    alert('Error updating quantity. Please try again.');
                }
            });
        });
    });
</script>


</body>

</html>