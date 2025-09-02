@forelse($cart as $item)
    <tr>
        <td class="d-flex align-items-center text-start" data-label="PRODUCT">
            <button class="btn btn-sm btn-link text-muted me-2 p-0 remove-item-btn" data-row-id="{{ $item['rowId'] }}">
                <i class="bi bi-x fs-4"></i>
            </button>
            <a href="{{ route('product.show', $item['slug']) }}">
                <img src="{{ $item['image'] ? asset('public/uploads/' . $item['image']) : 'https://placehold.co/100x100/F5F5F5/4B5563?text=No+Image' }}"
                     alt="{{ $item['name'] }}" class="spotlight_cart_item_image me-3">
            </a>
            <div class="spotlight_cart_item_details">
                <h6 class="mb-0 fw-semibold">
                    <a href="{{ route('product.show', $item['slug']) }}" class="text-dark text-decoration-none">{{ $item['name'] }}</a>
                </h6>
                <p class="text-muted small mb-0">{{ $item['size'] }} / {{ $item['color'] }}</p>
            </div>
        </td>
        <td data-label="PRICE" class="fw-semibold">৳ {{ number_format($item['price'], 2) }}</td>
        <td data-label="QUANTITY">
            <div class="spotlight_cart_item_quantity mx-auto">
                <button class="btn btn-light quantity-btn" data-row-id="{{ $item['rowId'] }}" data-action="minus">-</button>
                <span class="px-3" id="cart-quantity-{{ $item['rowId'] }}">{{ $item['quantity'] }}</span>
                <button class="btn btn-light quantity-btn" data-row-id="{{ $item['rowId'] }}" data-action="plus">+</button>
            </div>
        </td>
        <td data-label="SUBTOTAL" class="fw-semibold" id="cart-item-subtotal-{{ $item['rowId'] }}">
            ৳ {{ number_format($item['price'] * $item['quantity'], 2) }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-5">
            <p>Your cart is empty.</p>
            <a href="{{ route('shop.show') }}" class="btn btn-dark">Continue Shopping</a>
        </td>
    </tr>
@endforelse