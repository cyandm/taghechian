export function CartPage() {
  console.log("Cart page initialized");

  // AJAX Update Cart Quantity
  function updateCartQuantity(cartKey, quantity) {
    const cartItem = document.querySelector(
      `.cart-item[data-cart-key="${cartKey}"]`,
    );

    if (!cartItem) return;

    // Add loading state
    cartItem.classList.add("loading");

    fetch(cart_ajax_params.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "update_cart_quantity",
        cart_key: cartKey,
        quantity: quantity,
        security: cart_ajax_params.nonce,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update item subtotal
          const subtotalElement = cartItem.querySelector(".item-subtotal");
          if (subtotalElement && data.data.item_subtotal) {
            subtotalElement.innerHTML = data.data.item_subtotal;
          }

          // Update cart totals
          if (data.data.cart_subtotal) {
            const cartSubtotal = document.querySelector(
              ".cart-subtotal-amount",
            );
            if (cartSubtotal) cartSubtotal.innerHTML = data.data.cart_subtotal;
          }

          if (data.data.cart_total) {
            const cartTotal = document.querySelector(".cart-total-amount");
            if (cartTotal) cartTotal.innerHTML = data.data.cart_total;
          }

          // Remove item if quantity is 0
          if (quantity === 0) {
            cartItem.remove();

            // Check if cart is empty
            const remainingItems = document.querySelectorAll(".cart-item");
            if (remainingItems.length === 0) {
              location.reload();
            }
          }
        } else {
          alert(data.data.message || "خطا در بروزرسانی سبد خرید");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("خطا در ارتباط با سرور");
      })
      .finally(() => {
        cartItem.classList.remove("loading");
      });
  }

  // Handle +/- buttons
  document.querySelectorAll(".quantity-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const cartKey = this.dataset.cartKey;
      const input = this.parentElement.querySelector("input.qty");
      const currentQty = parseInt(input.value) || 0;
      const maxQty = parseInt(input.getAttribute("max")) || 9999;
      const minQty = parseInt(input.getAttribute("min")) || 0;

      let newQty;

      if (this.classList.contains("quantity-plus")) {
        newQty = Math.min(currentQty + 1, maxQty);
      } else {
        newQty = Math.max(currentQty - 1, minQty);
      }

      if (newQty !== currentQty) {
        input.value = newQty;
        updateCartQuantity(cartKey, newQty);
      }
    });
  });

  // Handle manual input change
  document.querySelectorAll("input.qty").forEach((input) => {
    input.addEventListener("change", function () {
      const cartKey = this.closest(".cart-item").dataset.cartKey;
      const newQty = parseInt(this.value) || 0;
      const maxQty = parseInt(this.getAttribute("max")) || 9999;
      const minQty = parseInt(this.getAttribute("min")) || 0;

      const validQty = Math.max(minQty, Math.min(newQty, maxQty));
      this.value = validQty;

      updateCartQuantity(cartKey, validQty);
    });
  });

  // Handle remove item
  document.querySelectorAll(".remove-item").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      if (confirm("آیا از حذف این محصول اطمینان دارید؟")) {
        const cartKey = this.closest(".cart-item").dataset.cartKey;
        updateCartQuantity(cartKey, 0);
      }
    });
  });
}
