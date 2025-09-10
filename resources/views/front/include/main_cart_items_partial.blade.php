@forelse($cart as $item)
    @if(isset($item['is_bundle']) && $item['is_bundle'])
        {{-- ======================= BUNDLE ITEM DISPLAY ======================= --}}
        <tr class="main-cart-item">
            {{-- Main Bundle Product Info --}}
            <td class="d-flex align-items-center text-start" data-label="PRODUCT">
                <button class="btn btn-sm btn-link text-muted me-2 p-0 remove-main-cart-item" data-row-id="{{ $item['rowId'] }}">
                    <i class="bi bi-x-lg"></i>
                </button>
                <img src="{{ $item['image'] ?? 'https://placehold.co/100x100' }}"
                     alt="{{ $item['name'] }}" class="spotlight_cart_item_image me-3">
                <div class="spotlight_cart_item_details">
                    <a href="{{ $item['url'] }}" class="text-decoration-none text-dark">
                        <h6 class="mb-0 fw-semibold">{{ $item['name'] }}</h6>
                    </a>
                    <p class="text-muted small mb-0">Combo Bundle</p>
                </div>
            </td>
            {{-- Bundle Price --}}
            <td data-label="PRICE" class="fw-semibold">৳{{ number_format($item['price'], 2) }}</td>
            {{-- Bundle Quantity --}}
            <td data-label="QUANTITY">
                <div class="spotlight_cart_item_quantity mx-auto">
                    {{-- Note: The JS for these buttons needs to target update-main-cart-item --}}
                    <button class="btn btn-light update-main-cart-item" data-row-id="{{ $item['rowId'] }}" data-action="decrease">-</button>
                    <span class="px-2">{{ $item['quantity'] }}</span>
                    <button class="btn btn-light update-main-cart-item" data-row-id="{{ $item['rowId'] }}" data-action="increase">+</button>
                </div>
            </td>
            {{-- Bundle Subtotal --}}
            <td data-label="SUBTOTAL" class="fw-semibold">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
        </tr>

        {{-- Loop and display selected products for the bundle --}}
        @foreach($item['selected_products'] as $subProduct)
        <tr class="spotlight_cart_nested_item">
            <td data-label="PRODUCT" class="text-start">
                <div class="d-flex align-items-center">
                    <img src="{{ $subProduct['image'] ?? 'https://placehold.co/50x50' }}"
                         alt="{{ $subProduct['name'] }}"
                         class="spotlight_cart_nested_item_image me-2">
                    <div class="spotlight_cart_nested_item_details">
                        <h6 class="mb-0 text-muted small">{{ $subProduct['name'] }} - {{ $subProduct['size'] }}</h6>
                        @if(!empty($subProduct['color']))
                        <p class="text-muted small mb-0">Color: {{ $subProduct['color'] }}</p>
                        @endif
                    </div>
                </div>
            </td>
            <td data-label="PRICE" class="fw-semibold text-center text-muted"><small>৳{{ number_format($subProduct['price'], 2) }}</small></td>
            <td data-label="QUANTITY" class="text-center text-muted">{{ $item['quantity'] }}</td>
            <td data-label="SUBTOTAL" class="fw-semibold text-center text-muted"><small>৳{{ number_format($subProduct['price'] * $item['quantity'], 2) }}</small></td>
        </tr>
        @endforeach

    @else
        {{-- ======================= REGULAR ITEM DISPLAY ======================= --}}
        <tr class="main-cart-item">
            <td class="d-flex align-items-center text-start" data-label="PRODUCT">
                <button class="btn btn-sm btn-link text-muted me-2 p-0 remove-main-cart-item" data-row-id="{{ $item['rowId'] }}">
                    <i class="bi bi-x-lg"></i>
                </button>
                
                <img src="{{$front_ins_url . 'public/uploads/' . $item['image']  ?? 'https://placehold.co/100x100' }}"
                     alt="{{ $item['name'] }}" class="spotlight_cart_item_image me-3">
                <div class="spotlight_cart_item_details">
                    <a href="{{ $item['url'] ?? '#' }}" class="text-decoration-none text-dark">
                        <h6 class="mb-0 fw-semibold">{{ $item['name'] }}</h6>
                    </a>
                    <p class="text-muted small mb-0">Size: {{ $item['size'] }}</p>
                    @if(!empty($item['color']) && $item['color'] !== 'N/A')
                        <p class="text-muted small mb-0">Color: {{ $item['color'] }}</p>
                    @endif
                </div>
            </td>
            <td data-label="PRICE" class="fw-semibold">৳{{ number_format($item['price'], 2) }}</td>
            <td data-label="QUANTITY">
                <div class="spotlight_cart_item_quantity mx-auto">
                     <button class="btn btn-light update-main-cart-item" data-row-id="{{ $item['rowId'] }}" data-action="decrease">-</button>
                     <span class="px-2">{{ $item['quantity'] }}</span>
                     <button class="btn btn-light update-main-cart-item" data-row-id="{{ $item['rowId'] }}" data-action="increase">+</button>
                </div>
            </td>
            <td data-label="SUBTOTAL" class="fw-semibold">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="4" class="text-center py-5">
            <p>Your cart is currently empty.</p>
        </td>
    </tr>
@endforelse