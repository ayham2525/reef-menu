<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Café Reef - Order</title>

  {{-- New UI stylesheet --}}
  <link rel="stylesheet" href="{{ asset('css/menu.css') }}" />



</head>

<body>

  <!-- Header -->
  <header class="header px py">
    <!-- Logo -->
    <div class="logo">
      <img src="{{ asset('assets/reef-order/reef-logo.svg') }}" alt="logo" width="100" height="100" />
    </div>

    <!-- Cart Button (Mobile/Tablet only) -->
   <button class="btn-circle cart-button" id="cartButton" type="button" aria-label="Open cart">
  <span class="cart-icon-wrap">
    <img src="{{ asset('assets/reef-order/reef-view-selected-icon.svg') }}" alt="cart" width="24" height="24" />
    <span class="cart-badge" id="cartBadge">0</span>
  </span>
</button>
  </header>

  <!-- Main Layout -->
  <div class="main-layout">

    <!-- Left Side: Products -->
    <div class="products-section">

      <!-- Filter Bar -->
      <div class="filter-container px">
        <!-- Search -->
        <div class="search-box">
          <input type="text" placeholder="Search" class="search-input" id="searchInput" />
        </div>

        <!-- Scrollable Filter Pills -->
        <div class="filter-pills-wrapper">
          <div class="filter-pills" id="filterPills">
            {{-- pills will be rendered by JS --}}
            <button class="filter-pill active" data-category="all" type="button">All</button>
            <div class="filter-pill-fade"></div>
          </div>
        </div>

        <!-- Filter popup button -->
        <button class="btn-circle filter-btn" id="filterBtn" type="button">
          <img src="{{ asset('assets/reef-order/reef-filter-icon.svg') }}" alt="filter" width="24" height="24" />
        </button>
      </div>

      <!-- Product Grid -->
      <div class="product-grid px py" id="productGrid"></div>
    </div>

    <!-- Right Side: Order Sidebar -->
    <div class="order-sidebar-wrapper px" id="sidebarWrapper">
      <div class="order-sidebar py">

        <div class="order-header mx">
          <h3>Order</h3>
          <div class="order-header-right">
            <p class="item-count" id="itemCount">0 items</p>
            <button class="btn-circle close-sidebar-btn" id="closeSidebarBtn" type="button">✕</button>
          </div>
        </div>

        <div class="order-items mx" id="orderItems"></div>

        <div class="order-actions mx">
          <button class="btn-primary place-order-btn" id="placeOrderBtn" type="button" disabled>
            Place Order
            <img class="arrow" src="{{ asset('assets/reef-order/reef-white-arrow-right-icon.svg') }}" alt="arrow" width="24" height="24" />
          </button>

          <button class="btn-circle clear-order-btn" id="clearOrderBtn" type="button" style="display:none;">
            <img src="{{ asset('assets/reef-order/reef-trash-icon.svg') }}" alt="clear" width="20" height="20" />
          </button>
        </div>

      </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
  </div>

  <!-- Filter Popup (Bottom Sheet) -->
  <div class="filter-popup-overlay" id="filterPopupOverlay"></div>
  <div class="filter-popup" id="filterPopup">
    <div class="filter-popup-header">
      <h3>Filters</h3>
      <button class="btn-circle filter-popup-close" id="filterPopupClose" type="button">✕</button>
    </div>
    <div class="filter-popup-content" id="filterPopupContent"></div>
  </div>

  <!-- Fixed View Order Button (Mobile/Tablet) -->
  <button class="btn-primary view-order-btn" id="viewOrderBtn" type="button">
    <span class="view-order-text">View Order</span>
    <span class="view-order-count" id="viewOrderCount">0</span>
  </button>


  <script>
    window.SERVER_DATA = {
      items: @json($items),
      categories: @json($categories),
      vatRate: @json($vatRate),
      csrf: "{{ csrf_token() }}",
      orderUrl: "{{ route('orders.store') }}",

      // assets helpers for JS
      assets: {
        fallbackImage: "{{ asset('assets/reef-order/fallback-image.svg') }}",
        plusIcon: "{{ asset('assets/reef-order/reef-plus-icon.svg') }}",
        minusIcon: "{{ asset('assets/reef-order/reef-minus-icon.svg') }}"
      }
    };
  </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/menu.js') }}"></script>
</body>
</html>
