    <!-- Desktop & Tablet Header (Hidden on mobile) -->

    <header class="d-none d-lg-block sticky-header">
        <!-- Top Section -->
        <div class="header-top p-3 d-flex align-items-center" style="background-color: {{ $headerColor }} !important;">
            <div class="container d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <a class="logo-container" href="{{route('home.index')}}">
                    <img src="{{$front_ins_url}}{{$front_logo_name}}" alt="Spotlighy Attires">
                </a>

                <!-- Search Bar -->
                <div class="search-container">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for products">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>

                <!-- Support -->
                <div class="d-flex align-items-center">
                    <i class="bi bi-headset me-2 fs-4"></i>
                    <div>
                        <div class="fw-bold">24 Support</div>
                        <div>+880 1872-094599</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bottom Section -->
        <div class="header-bottom p-3">
            <div class="container d-flex justify-content-between align-items-center">
                <!-- Left Side: All Categories & Sub-menu items -->
                <div class="d-flex align-items-center">
                    <button class="btn btn-all-categories me-3" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#categoriesOffcanvas" aria-controls="categoriesOffcanvas">
                        <i class="bi bi-list me-2"></i> All Category
                    </button>
                    <!-- Added the Menu link here -->
                    <a class="nav-link text-dark mx-2 d-none d-xl-block" href="#" data-bs-toggle="offcanvas"
                        data-bs-target="#menuOffcanvas" aria-controls="menuOffcanvas">Menu</a>
                    @if(isset($menuItems) && $menuItems->count() > 0)
    @foreach($menuItems as $item)
        <a class="nav-link text-dark mx-2 d-none d-xl-block" href="{{ url($item->route) }}">{{ $item->name }}</a>
    @endforeach
@endif
                </div>

                <!-- Right Side: Icons -->
                <div class="d-flex align-items-center">
                    <!-- Updated User icon to trigger the new offcanvas -->
                    <a class="nav-link text-dark me-3" href="#" data-bs-toggle="offcanvas"
                        data-bs-target="#signInOffcanvas" aria-controls="signInOffcanvas">
                        <i class="bi bi-person-circle fs-4"></i>
                    </a>
                    <a class="nav-link text-dark me-3" href="#">
                        <i class="bi bi-heart fs-4"></i>
                    </a>
                    <a class="nav-link text-dark me-3" href="#">
                        <i class="bi bi-arrow-left-right fs-4"></i>
                    </a>
                    <!-- Updated Cart icon to trigger the new offcanvas -->
                    <a class="nav-link text-dark" href="#" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas"
                        aria-controls="cartOffcanvas">
                        <i class="bi bi-cart fs-4"></i>
                        <span id="desktop-cart-count" class="badge rounded-pill bg-dark" style="font-size: 0.6em; padding: .35em .5em;">0</span>

                    </a>
                </div>
            </div>
        </div>
    </header>


    <!-- Mobile Header (Hidden on desktop/tablet) -->
    <header class="d-lg-none sticky-header">
        <!-- Top Section -->
        <nav class="navbar navbar-dark p-3">
            <div class="container-fluid">
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileOffcanvas" aria-controls="mobileOffcanvas">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand mx-auto" href="{{route('home.index')}}">
                    <img src="{{$front_ins_url}}{{$front_logo_name}}" style="width: 120px;height:21px;" alt="Random Store">
                </a>
                <!-- Updated User icon to trigger the new offcanvas -->
                <a class="nav-link text-light" href="#" data-bs-toggle="offcanvas" data-bs-target="#signInOffcanvas"
                    aria-controls="signInOffcanvas">
                    <i class="bi bi-person-circle fs-4"></i>
                </a>
            </div>
        </nav>
        <!-- Bottom Section: Search -->
        <div class="mobile-search-bar">
            <form class="d-flex">
                <div class="input-group">
                    <input class="form-control rounded-pill" type="search" placeholder="Search for products"
                        aria-label="Search">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
            </form>
        </div>
    </header>

    <!-- Offcanvas for Desktop/Tablet Categories -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="categoriesOffcanvas" aria-labelledby="categoriesOffcanvasLabel">
    <div class="offcanvas-header bg-dark text-white">
        <h5 class="offcanvas-title" id="categoriesOffcanvasLabel">All Categories</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">

            @if(isset($sidebarCategories))
                @foreach($sidebarCategories as $category)
                    {{-- Check if the category has any visible subcategories --}}
                    @if($category->subcategories->isNotEmpty())
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center has-submenu"
                           data-bs-toggle="collapse" href="#submenu-{{ $category->id }}" role="button" aria-expanded="false"
                           aria-controls="submenu-{{ $category->id }}">
                           {{-- You can add icons here if you store them in the database --}}
                           {{ $category->name }} <i class="bi bi-chevron-right ms-auto"></i>
                        </a>
                        <div class="collapse" id="submenu-{{ $category->id }}">
                            @foreach($category->subcategories as $subcategory)
                                <a href="{{route('subcategory.show', $subcategory->slug)}}" class="list-group-item list-group-item-action ps-5">{{ $subcategory->name }}</a>
                            @endforeach
                        </div>
                    @else
                        <a href="{{route('category.show', $category->slug)}}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                           {{ $category->name }} <i class="bi bi-chevron-right ms-auto"></i>
                        </a>
                    @endif
                @endforeach
            @endif

        </div>
    </div>
</div>

    <!-- New Offcanvas for Desktop Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuOffcanvas" aria-labelledby="menuOffcanvasLabel">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title" id="menuOffcanvasLabel">Menu</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <a href="{{route('home.index')}}" class="list-group-item list-group-item-action">Home</a>
                <a href="#" class="list-group-item list-group-item-action">About Us</a>
                <a href="#" class="list-group-item list-group-item-action">Contact</a>
            </div>
        </div>
    </div>

    <!-- Offcanvas for Mobile Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileOffcanvas" aria-labelledby="mobileOffcanvasLabel">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title" id="mobileOffcanvasLabel">Main Menu</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <!-- Tabbed navigation -->
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-categories-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-categories" type="button" role="tab" aria-controls="nav-categories"
                        aria-selected="true">CATEGORIES</button>
                    <button class="nav-link" id="nav-menu-tab" data-bs-toggle="tab" data-bs-target="#nav-menu"
                        type="button" role="tab" aria-controls="nav-menu" aria-selected="false">MENU</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- Categories Tab Content -->
                <div class="tab-pane fade show active" id="nav-categories" role="tabpanel"
                    aria-labelledby="nav-categories-tab">
                    <div class="list-group list-group-flush">
                        @if(isset($sidebarCategories))
                @foreach($sidebarCategories as $category)
                    @if($category->subcategories->isNotEmpty())
                        <!-- Category with Submenu -->
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center has-submenu"
                           data-bs-toggle="collapse" href="#mobile-submenu-{{ $category->id }}" role="button" aria-expanded="false"
                           aria-controls="mobile-submenu-{{ $category->id }}">
                            {{-- You can add an icon field to your category table for this --}}
                            <i class="bi bi-tag me-2"></i> {{ $category->name }} <i class="bi bi-chevron-right ms-auto"></i>
                        </a>
                        <div class="collapse" id="mobile-submenu-{{ $category->id }}">
                            @foreach($category->subcategories as $subcategory)
                                <a href="{{route('subcategory.show', $subcategory->slug)}}" class="list-group-item list-group-item-action ps-5">{{ $subcategory->name }}</a>
                            @endforeach
                        </div>
                    @else
                        <!-- Category without Submenu -->
                        <a href="{{route('category.show', $category->slug)}}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <i class="bi bi-tag me-2"></i> {{ $category->name }} <i class="bi bi-chevron-right ms-auto"></i>
                        </a>
                    @endif
                @endforeach
            @endif
                    </div>
                </div>
                <!-- Menu Tab Content -->
                <div class="tab-pane fade" id="nav-menu" role="tabpanel" aria-labelledby="nav-menu-tab">
                    <div class="list-group list-group-flush">
                        <a href="{{route('home.index')}}" class="list-group-item list-group-item-action">Home</a>
                        <a href="#" class="list-group-item list-group-item-action">About Us</a>
                        <a href="#" class="list-group-item list-group-item-action">Contact</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas for Sign In Form -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="signInOffcanvas" aria-labelledby="signInOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="signInOffcanvasLabel">Sign in</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form>
                <div class="mb-3">
                    <label for="usernameEmail" class="form-label">Username or email address *</label>
                    <input type="text" class="form-control" id="usernameEmail" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password *</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-dark">Log In</button>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="text-decoration-none text-dark">Lost your password?</a>
                </div>
            </form>
            <hr>
            <div class="text-center">
                <i class="bi bi-person-circle fs-1 text-secondary"></i>
                <p class="mt-3">No account yet?</p>
                <a href="#" class="btn btn-outline-dark rounded-pill px-4">Create An Account</a>
            </div>
        </div>
    </div>

    <!-- Offcanvas for Shopping Cart -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="cartOffcanvasLabel">Shopping cart</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Scrollable product list -->
            <div class="cart-products">
              <div class="text-center p-5">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            </div>

            <!-- Fixed bottom section -->
            <div class="cart-fixed-bottom">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" style="color:black;">Subtotal:</h5>
                <h5 class="mb-0 fw-bold" id="cart-subtotal" style="color:black;">৳ 0.00</h5>
            </div>
            <p class="text-muted small" id="cart-shipping-message">
                Shipping and taxes calculated at checkout.
            </p>
            <a href="#" class="btn btn-outline-dark">View Cart</a>
            <a href="#" class="btn btn-dark">Checkout</a>
        </div>
        </div>
    </div>

    <!-- Sticky Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav d-lg-none">
        <a href="{{ route('shop.show') }}" class="nav-link text-dark">
            <i class="bi bi-shop"></i>
            <span>Shop</span>
        </a>
        <a href="#" class="nav-link text-dark">
            <div class="position-relative">
                <i class="bi bi-heart"></i>
                <span class="badge rounded-pill bg-danger">0</span>
            </div>
            <span>Wishlist</span>
        </a>
        <a href="#" class="nav-link text-dark" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas"
            aria-controls="cartOffcanvas">
            <div class="position-relative">
                <i class="bi bi-cart"></i>
                 <span id="mobile-cart-count" class="badge rounded-pill bg-dark">0</span>
            </div>
            <span>Cart</span>
        </a>
        <a href="#" class="nav-link text-dark" data-bs-toggle="offcanvas" data-bs-target="#mobileOffcanvas"
            aria-controls="mobileOffcanvas">
            <i class="bi bi-list"></i>
            <span>Menu</span>
        </a>
    </nav>