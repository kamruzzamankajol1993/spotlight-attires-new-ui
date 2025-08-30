@forelse($cart as $item)
<div class="cart-product-item" id="cart-item-{{ $item['rowId'] }}">
    @if($item['image'])
        <img src="{{ $front_ins_url . 'public/uploads/' . $item['image'] }}" alt="{{ $item['name'] }}">
    @else
        <img src="https://placehold.co/80x80" alt="Placeholder Image">
    @endif
    <div class="product-details">
        <a href="{{ route('product.show', $item['slug']) }}" class="text-dark text-decoration-none">
            <h6 class="mb-0">{{ $item['name'] }} - {{ $item['size'] }}</h6>
        </a>
        <small class="text-muted">Color: {{ $item['color'] }}</small>
        <div class="d-flex align-items-center mt-2">

            <div class="d-flex align-items-center border rounded-3 overflow-hidden me-3" style="width: 80px;">
                <button class="btn btn-sm btn-light rounded-0 cart-quantity-btn" data-row-id="{{ $item['rowId'] }}" data-change="-1">-</button>
                <span class="px-2 small cart-quantity-value">{{ $item['quantity'] }}</span>
                <button class="btn btn-sm btn-light rounded-0 cart-quantity-btn" data-row-id="{{ $item['rowId'] }}" data-change="1">+</button>
            </div>

            <span class="price fw-bold">৳ {{ number_format($item['price'] * $item['quantity'], 2) }}</span>
        </div>
    </div>
    <button type="button" class="btn-close remove-cart-item" data-row-id="{{ $item['rowId'] }}" aria-label="Remove item"></button>
</div>
@empty
<div class="text-center p-5">
    <i class="bi bi-cart-x fs-1 text-muted"></i>
    <p class="mt-3">Your cart is currently empty.</p>
</div>
@endforelse