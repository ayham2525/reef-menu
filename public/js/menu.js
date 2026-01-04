/*
|--------------------------------------------------------------------------
| Reef Order UI - Laravel (SERVER_DATA)
|--------------------------------------------------------------------------
*/

const DATA = window.SERVER_DATA || {};
const rawItems = Array.isArray(DATA.items) ? DATA.items : [];
const rawCategories = Array.isArray(DATA.categories) ? DATA.categories : [];

const ASSETS = {
  fallbackImage: DATA.assets?.fallbackImage || DATA.assets?.fallback || "/assets/reef-order/fallback-image.svg",
  plusIcon: DATA.assets?.plusIcon || DATA.assets?.plus || "/assets/reef-order/reef-plus-icon.svg",
  minusIcon: DATA.assets?.minusIcon || DATA.assets?.minus || "/assets/reef-order/reef-minus-icon.svg",
};

let cart = {};
let currentCategory = "all";
let currentSearch = "";

/* --------------------------
  Helpers
--------------------------- */
function catSlug(c) { return c?.slug ?? c?.id ?? ""; }
function catName(c) { return c?.name ?? ""; }

function itemCategorySlug(item) {
  return item?.category_id ?? item?.category ?? item?.categorySlug ?? item?.category_slug ?? "";
}
function itemTitle(item) { return item?.name ?? item?.title ?? ""; }
function itemCategoryLabel(item) {
  return item?.category_label ?? item?.category_name ?? item?.categoryLabel ?? item?.categoryName ?? "";
}
function itemImage(item) {
  const img = item?.img ?? item?.image ?? item?.photo;
  return img || ASSETS.fallbackImage;
}

/* --------------------------
  DOM
--------------------------- */
const filterPillsEl = document.getElementById("filterPills");
const productGridEl = document.getElementById("productGrid");
const orderItemsEl = document.getElementById("orderItems");

const itemCountEl = document.getElementById("itemCount");
const cartBadgeEl = document.getElementById("cartBadge");

const placeOrderBtn = document.getElementById("placeOrderBtn");
const clearOrderBtn = document.getElementById("clearOrderBtn");
const searchInput = document.getElementById("searchInput");

const cartButton = document.getElementById("cartButton");
const viewOrderBtn = document.getElementById("viewOrderBtn");
const closeSidebarBtn = document.getElementById("closeSidebarBtn");
const sidebarWrapper = document.getElementById("sidebarWrapper");
const sidebarOverlay = document.getElementById("sidebarOverlay");

const filterBtn = document.getElementById("filterBtn");
const filterPopup = document.getElementById("filterPopup");
const filterPopupOverlay = document.getElementById("filterPopupOverlay");
const filterPopupClose = document.getElementById("filterPopupClose");
const filterPopupContent = document.getElementById("filterPopupContent");

const viewOrderCount = document.getElementById("viewOrderCount");

/* --------------------------
  Sidebar / Popup
--------------------------- */
function toggleSidebar(forceOpen = null) {
  const shouldOpen = forceOpen === null ? !sidebarWrapper.classList.contains("active") : forceOpen;
  sidebarWrapper.classList.toggle("active", shouldOpen);
  sidebarOverlay.classList.toggle("active", shouldOpen);
  document.body.classList.toggle("sidebar-open", shouldOpen);
}
function toggleFilterPopup(forceOpen = null) {
  const shouldOpen = forceOpen === null ? !filterPopup.classList.contains("active") : forceOpen;
  filterPopup.classList.toggle("active", shouldOpen);
  filterPopupOverlay.classList.toggle("active", shouldOpen);
  document.body.classList.toggle("filter-popup-open", shouldOpen);
}

/* --------------------------
  ✅ Toast/Notify (NO alert)
--------------------------- */
function ensureToastContainer() {
  let el = document.getElementById("toastContainer");
  if (el) return el;

  el = document.createElement("div");
  el.id = "toastContainer";
  el.style.position = "fixed";
  el.style.right = "16px";
  el.style.bottom = "16px";
  el.style.zIndex = "99999";
  el.style.display = "flex";
  el.style.flexDirection = "column";
  el.style.gap = "10px";
  document.body.appendChild(el);
  return el;
}

function showToast(message, type = "info") {
  const container = ensureToastContainer();

  const toast = document.createElement("div");
  toast.className = `reef-toast reef-toast-${type}`;
  toast.textContent = message;

  // inline styling (so you don't need CSS file changes)
  toast.style.padding = "12px 14px";
  toast.style.borderRadius = "12px";
  toast.style.boxShadow = "0 8px 24px rgba(0,0,0,.12)";
  toast.style.background = "#111";
  toast.style.color = "#fff";
  toast.style.fontSize = "13px";
  toast.style.maxWidth = "320px";
  toast.style.lineHeight = "1.4";
  toast.style.opacity = "0";
  toast.style.transform = "translateY(6px)";
  toast.style.transition = "all .18s ease";

  if (type === "success") toast.style.background = "#16a34a";
  if (type === "error") toast.style.background = "#dc2626";
  if (type === "warning") toast.style.background = "#f59e0b";

  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.style.opacity = "1";
    toast.style.transform = "translateY(0)";
  });

  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateY(6px)";
    setTimeout(() => toast.remove(), 200);
  }, 2500);
}

function notify(type, title, text) {
  // Use SweetAlert2 if available (nice UI) — NO alert()
  if (window.Swal) {
    const icon = type; // success|error|warning|info
    Swal.fire({
      toast: true,
      position: "bottom-end",
      icon,
      title: title || "",
      text: text || "",
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true,
    });
    return;
  }

  // fallback toast
  showToast(text || title || "Done", type);
}

/* --------------------------
  ✅ Ensure Agency Field Exists (Auto-inject)
--------------------------- */
function ensureAgencyField() {
  let agencyField = document.getElementById("agencyField");
  let agencyNameInput = document.getElementById("agencyName");
  let agencyHint = document.getElementById("agencyHint");

  if (agencyField && agencyNameInput && agencyHint) return { agencyNameInput, agencyHint };

  const actionsEl = placeOrderBtn?.closest(".order-actions");
  if (!actionsEl) return { agencyNameInput: null, agencyHint: null };

  agencyField = document.createElement("div");
  agencyField.className = "agency-field mx";
  agencyField.id = "agencyField";
  agencyField.innerHTML = `
    <label for="agencyName" class="agency-label">
      Name <span class="agency-required">*</span>
    </label>
    <input
      type="text"
      id="agencyName"
      class="agency-input"
      placeholder="Enter name"
      autocomplete="organization"
      required
    />
    <p class="agency-hint" id="agencyHint">Required to place the order.</p>
  `;

  actionsEl.parentNode.insertBefore(agencyField, actionsEl);

  agencyNameInput = document.getElementById("agencyName");
  agencyHint = document.getElementById("agencyHint");

  agencyNameInput?.addEventListener("input", () => {
    validateAgency(false);
    updateCartUI();
  });

  return { agencyNameInput, agencyHint };
}

/* --------------------------
  ✅ Agency validation (NO alert)
--------------------------- */
function getAgencyName() {
  const { agencyNameInput } = ensureAgencyField();
  return (agencyNameInput?.value || "").trim();
}

function validateAgency(showError = false) {
  const { agencyNameInput, agencyHint } = ensureAgencyField();
  if (!agencyNameInput) {
    if (showError) {
      notify("error", "Missing field", "Name field not found in HTML.");
    }
    return false;
  }

  const ok = getAgencyName().length > 0;

  if (showError) {
    agencyNameInput.classList.toggle("invalid", !ok);
    agencyHint?.classList.toggle("error", !ok);
    if (agencyHint) agencyHint.textContent = ok ? "Required to place the order." : "Name is required.";
    if (!ok) agencyNameInput.focus();
  } else {
    if (ok) {
      agencyNameInput.classList.remove("invalid");
      agencyHint?.classList.remove("error");
      if (agencyHint) agencyHint.textContent = "Required to place the order.";
    }
  }

  return ok;
}

/* --------------------------
  Render: Filter Pills
--------------------------- */
function renderFilterPills() {
  filterPillsEl.innerHTML = "";

  const allBtn = document.createElement("button");
  allBtn.className = "filter-pill" + (currentCategory === "all" ? " active" : "");
  allBtn.dataset.category = "all";
  allBtn.type = "button";
  allBtn.textContent = "All";
  filterPillsEl.appendChild(allBtn);

  rawCategories.forEach((c) => {
    const slug = catSlug(c);
    const name = catName(c);
    if (!slug) return;

    const btn = document.createElement("button");
    btn.className = "filter-pill" + (currentCategory === slug ? " active" : "");
    btn.dataset.category = slug;
    btn.type = "button";
    btn.textContent = name || slug;
    filterPillsEl.appendChild(btn);
  });

  const fade = document.createElement("div");
  fade.className = "filter-pill-fade";
  filterPillsEl.appendChild(fade);

  filterPillsEl.querySelectorAll(".filter-pill").forEach((pill) => {
    pill.addEventListener("click", () => setActiveCategory(pill.dataset.category));
  });

  populateFilterPopup();
}

function populateFilterPopup() {
  const mainPills = Array.from(document.querySelectorAll(".filter-pills .filter-pill"));
  filterPopupContent.innerHTML = mainPills
    .map((pill) => {
      const category = pill.dataset.category;
      const text = pill.textContent.trim();
      const isActive = pill.classList.contains("active");
      return `<button class="filter-pill ${isActive ? "active" : ""}" data-category="${category}" type="button">${text}</button>`;
    })
    .join("");
}

function setActiveCategory(category) {
  currentCategory = category || "all";
  document.querySelectorAll(".filter-pills .filter-pill").forEach((p) => p.classList.remove("active"));
  const activePill = document.querySelector(`.filter-pills .filter-pill[data-category="${currentCategory}"]`);
  if (activePill) activePill.classList.add("active");
  populateFilterPopup();
  renderProducts();
}

/* --------------------------
  Render: Products
--------------------------- */
function getFilteredItems() {
  const term = (currentSearch || "").toLowerCase().trim();
  return rawItems.filter((it) => {
    const title = itemTitle(it).toLowerCase();
    const cat = String(itemCategorySlug(it));
    const categoryOk = currentCategory === "all" || String(cat) === String(currentCategory);
    const searchOk = !term || title.includes(term);
    return categoryOk && searchOk;
  });
}

function renderProducts() {
  const items = getFilteredItems();
  if (!items.length) {
    productGridEl.innerHTML = `<div class="empty-cart"><p>No items found</p></div>`;
    return;
  }

  productGridEl.innerHTML = items.map((it) => {
    const id = it.id;
    const title = itemTitle(it);
    const label = itemCategoryLabel(it) || "";
    const img = itemImage(it);
    const qty = cart[id] || 0;

    return `
      <div class="product-card ${qty > 0 ? "in-cart" : ""}" data-id="${id}">
        <div class="product-image-container">
          <img src="${img}" alt="${title}" class="product-image" loading="lazy"
               onerror="this.onerror=null; this.src='${ASSETS.fallbackImage}';" />
          <div class="product-overlay">
            <div class="product-overlay-content">
              <div class="quantity-control">
                ${
                  qty > 0
                    ? `
                      <button class="qty-btn minus" data-id="${id}" type="button">
                        <img src="${ASSETS.minusIcon}" alt="minus" width="40" height="40" />
                      </button>
                      <span class="qty-display">${qty}</span>
                      <button class="qty-btn plus" data-id="${id}" type="button">
                        <img src="${ASSETS.plusIcon}" alt="plus" width="40" height="40" />
                      </button>
                    `
                    : `
                      <button class="qty-btn plus" data-id="${id}" type="button">
                        <img src="${ASSETS.plusIcon}" alt="plus" width="40" height="40" />
                      </button>
                    `
                }
              </div>
            </div>
          </div>
        </div>
        <div class="product-info">
          <h4>${title}</h4>
          <p>${label}</p>
        </div>
      </div>
    `;
  }).join("");
}

/* --------------------------
  Cart
--------------------------- */
function addToCart(id) {
  cart[id] = (cart[id] || 0) + 1;
  updateCartUI();
}
function removeFromCart(id) {
  if (!cart[id]) return;
  cart[id] -= 1;
  if (cart[id] <= 0) delete cart[id];
  updateCartUI();
}

function updateCartUI() {
  ensureAgencyField();

  const cartIds = Object.keys(cart);
  const totalItems = cartIds.reduce((sum, id) => sum + (cart[id] || 0), 0);

  itemCountEl.textContent = `${totalItems} item${totalItems !== 1 ? "s" : ""}`;
  cartBadgeEl.textContent = totalItems;
  viewOrderCount.textContent = totalItems;

  const agencyOk = validateAgency(false);
  placeOrderBtn.disabled = totalItems === 0 || !agencyOk;

  clearOrderBtn.style.display = totalItems === 0 ? "none" : "flex";

  if (!cartIds.length) {
    orderItemsEl.innerHTML = `<div class="empty-cart"><p>Your cart is empty</p></div>`;
  } else {
    orderItemsEl.innerHTML = cartIds.map((id) => {
      const item = rawItems.find(x => String(x.id) === String(id));
      const title = itemTitle(item || {});
      const label = itemCategoryLabel(item || {}) || "";
      const img = item ? itemImage(item) : ASSETS.fallbackImage;

      return `
        <div class="cart-item">
          <div class="cart-item-info">
            <h4>${title}</h4>
            <p>${label}</p>
            <div class="cart-quantity-control">
              <button class="cart-qty-btn minus" data-id="${id}" type="button">
                <img src="${ASSETS.minusIcon}" alt="minus" width="30" height="30" />
              </button>
              <span class="cart-qty-display">${cart[id]}</span>
              <button class="cart-qty-btn plus" data-id="${id}" type="button">
                <img src="${ASSETS.plusIcon}" alt="plus" width="30" height="30" />
              </button>
            </div>
          </div>
          <div class="cart-item-image">
            <img src="${img}" alt="${title}" loading="lazy"
                 onerror="this.onerror=null; this.src='${ASSETS.fallbackImage}';" />
          </div>
        </div>
      `;
    }).join("");
  }

  renderProducts();
}

/* --------------------------
  Place Order (NO alert)
--------------------------- */
async function placeOrder() {
  const cartIds = Object.keys(cart);
  if (!cartIds.length) return;

  if (!validateAgency(true)) return;

  const agencyName = getAgencyName();

  // ✅ Build items the same way StoreOrderRequest expects
  const items = cartIds.map((id) => {
    const item = rawItems.find(x => String(x.id) === String(id));

    const itemName = (item?.name ?? item?.title ?? "Item").toString();

    // IMPORTANT: adjust key if your items use different field name
    const unitPriceRaw = item?.price ?? item?.unit_price ?? item?.selling_price ?? 0;
    const unitPrice = Number(unitPriceRaw);

    return {
      item_name: itemName,
      quantity: cart[id],
      unit_price: unitPrice,
      menu_item_id: item?.id ?? id, // keep for BOM deduction if you want
    };
  });

  const payload = {
    agency_name: agencyName,
    notes: "Order placed",
    items: items,
  };

  try {
    const res = await fetch(DATA.orderUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": DATA.csrf,
        "Accept": "application/json",
      },
      body: JSON.stringify(payload),
    });

    const body = await res.json().catch(() => ({}));

    if (!res.ok) {
      let msg = body?.message || "Order failed";
      if (body?.errors) msg = Object.values(body.errors).flat().join(" | ");
      notify("error", "Failed", msg);
      return;
    }

    cart = {};
    updateCartUI();
    notify("success", "Success", "Order sent successfully.");
    toggleSidebar(false);

  } catch (e) {
    notify("error", "Failed", "Network/server error. Check console and logs.");
  }
}



/* --------------------------
  Events
--------------------------- */
function setupEvents() {
  cartButton?.addEventListener("click", () => toggleSidebar());
  viewOrderBtn?.addEventListener("click", () => toggleSidebar(true));
  closeSidebarBtn?.addEventListener("click", () => toggleSidebar(false));
  sidebarOverlay?.addEventListener("click", () => toggleSidebar(false));

  productGridEl?.addEventListener("click", (e) => {
    const btn = e.target.closest(".qty-btn");
    if (!btn) return;
    const id = btn.dataset.id;
    if (!id) return;

    if (btn.classList.contains("plus")) addToCart(id);
    if (btn.classList.contains("minus")) removeFromCart(id);
  });

  orderItemsEl?.addEventListener("click", (e) => {
    const btn = e.target.closest(".cart-qty-btn");
    if (!btn) return;
    const id = btn.dataset.id;
    if (!id) return;

    if (btn.classList.contains("plus")) addToCart(id);
    if (btn.classList.contains("minus")) removeFromCart(id);
  });

  filterBtn?.addEventListener("click", () => toggleFilterPopup(true));
  filterPopupClose?.addEventListener("click", () => toggleFilterPopup(false));
  filterPopupOverlay?.addEventListener("click", () => toggleFilterPopup(false));

  filterPopupContent?.addEventListener("click", (e) => {
    const pill = e.target.closest(".filter-pill");
    if (!pill) return;
    setActiveCategory(pill.dataset.category);
    toggleFilterPopup(false);
  });

  searchInput?.addEventListener("input", (e) => {
    currentSearch = (e.target.value || "");
    renderProducts();
  });

  placeOrderBtn?.addEventListener("click", placeOrder);

  clearOrderBtn?.addEventListener("click", () => {
    if (!Object.keys(cart).length) return;
    if (confirm("Clear order?")) {
      cart = {};
      updateCartUI();
    }
  });
}

/* --------------------------
  Init
--------------------------- */
document.addEventListener("DOMContentLoaded", () => {
  renderFilterPills();
  renderProducts();
  ensureAgencyField();
  updateCartUI();
  setupEvents();
});
