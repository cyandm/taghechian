export function CartPage() {
  console.log("Cart page initialized");

  function updateCartQuantity(cartKey, quantity) {
    const cartItems = document.querySelectorAll(
      `.cart-item[data-cart-key="${cartKey}"]`,
    );

    if (!cartItems.length) return;

    // Add loading state
    cartItems.forEach((addLoading) => addLoading.classList.add("loading"));

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
        if (!data.success) {
          alert(data.data.message || "خطا در بروزرسانی سبد خرید");
          return;
        }

        // -------------------------
        // Update EACH cart item UI
        // -------------------------
        cartItems.forEach((cartItem) => {
          // Subtotal per item
          const subtotalElement = cartItem.querySelector(".item-subtotal");
          if (subtotalElement && data.data.item_subtotal) {
            subtotalElement.innerHTML = data.data.item_subtotal;
          }

          // Handle removing item (when quantity = 0)
          if (quantity === 0) {
            cartItem.remove();
          }
        });

        // If no items left, reload page
        if (quantity === 0) {
          const remaining = document.querySelectorAll(".cart-item");
          if (remaining.length === 0) location.reload();
        }

        // -------------------------
        // Update CART totals
        // --------------------------
        const cartSubtotal = document.querySelector(".cart-subtotal-amount");
        if (cartSubtotal && data.data.cart_subtotal) {
          cartSubtotal.innerHTML = data.data.cart_subtotal;
        }

        const cartTotal = document.querySelector(".cart-total-amount");
        if (cartTotal && data.data.cart_total) {
          cartTotal.innerHTML = data.data.cart_total;
        }

        const cartSaving = document.querySelector(".cart-saving-amount");
        if (cartSaving && data.data.cart_saving) {
          cartSaving.innerHTML = data.data.cart_saving;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("خطا در ارتباط با سرور");
      })
      .finally(() => {
        cartItems.forEach((removeLoading) =>
          removeLoading.classList.remove("loading"),
        );
      });
  }

  // ========== Quantity +/- buttons ==========
  document.querySelectorAll(".quantity-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const cartKey = this.dataset.cartKey;

      const container =
        this.closest(".quantity-container") || this.parentElement;
      const input = container.querySelector("input.product-quantity");

      const currentQty = parseInt(input.value) || 0;
      const maxQty = parseInt(input.getAttribute("max")) || 9999;
      const minQty = parseInt(input.getAttribute("min")) || 0;

      let newQty = this.classList.contains("quantity-plus")
        ? Math.min(currentQty + 1, maxQty)
        : Math.max(currentQty - 1, minQty);

      if (newQty !== currentQty) {
        input.value = newQty;
        updateCartQuantity(cartKey, newQty);
      }
    });
  });

  // ========== Manual input change ==========
  document.querySelectorAll("input.product-quantity").forEach((input) => {
    input.addEventListener("change", function () {
      const cartItem = this.closest(".cart-item");
      if (!cartItem) return;

      const cartKey = cartItem.dataset.cartKey;

      const newQty = parseInt(this.value) || 0;
      const maxQty = parseInt(this.getAttribute("max")) || 9999;
      const minQty = parseInt(this.getAttribute("min")) || 0;

      const validQty = Math.max(minQty, Math.min(newQty, maxQty));
      this.value = validQty;

      updateCartQuantity(cartKey, validQty);
    });
  });

  // ========== Remove item ==========
  document.querySelectorAll(".remove-item").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      if (confirm("آیا از حذف این محصول اطمینان دارید؟")) {
        const cartItem = this.closest(".cart-item");
        if (!cartItem) return;

        const cartKey = cartItem.dataset.cartKey;

        updateCartQuantity(cartKey, 0);
      }
    });
  });

  jQuery(function ($) {
    var lastCouponMessage = "";

    function extractLiText(htmlString) {
      var $tmp = $("<div>").html(htmlString);
      var text = $tmp
        .find("ul.woocommerce-error li, ul.woocommerce-message li")
        .first()
        .text()
        .trim();
      if (!text) {
        text = $tmp
          .find(".woocommerce-error, .woocommerce-message")
          .first()
          .text()
          .trim();
      }
      return text || htmlString;
    }

    function renderMessage($container, text) {
      lastCouponMessage = text;
      $container.find(".coupon_code_response").text(text);
    }

    $("body").on("updated_checkout", function () {
      if (lastCouponMessage) {
        $(".coupon_code_loader").each(function () {
          $(this).find(".coupon_code_response").text(lastCouponMessage);
        });
      }
    });

    $(document).on(
      "click",
      ".coupon_code_loader .apply-coupon-btn",
      function (e) {
        e.preventDefault();

        var $container = $(this).closest(".coupon_code_loader");
        var $btn = $(this);
        var coupon = $container.find(".coupon_code").val();

        $btn.prop("disabled", true);
        $container
          .find(".coupon_code_response")
          .html(
            '<div class="flex items-center gap-2 text-cynBlack/70">' +
              '<span class="coupon-spinner"></span>' +
              "<span>در حال بررسی کد تخفیف...</span>" +
              "</div>",
          );

        $.ajax({
          type: "POST",
          url: wc_checkout_params.wc_ajax_url.replace(
            "%%endpoint%%",
            "apply_coupon",
          ),
          data: {
            coupon_code: coupon,
            security: wc_checkout_params.apply_coupon_nonce,
          },
          success: function (response) {
            var messageText = "";

            if (response && typeof response === "object" && response.messages) {
              messageText = extractLiText(response.messages);
            } else if (typeof response === "string") {
              messageText = extractLiText(response);
            } else {
              messageText = "پاسخ نامعتبر از سرور دریافت شد.";
            }

            renderMessage($container, messageText);

            $("body").trigger("update_checkout");
          },
          error: function () {
            renderMessage($container, "خطا در ارتباط با سرور!");
          },
          complete: function () {
            $btn.prop("disabled", false);
          },
        });
      },
    );
  });

  jQuery(function ($) {
    $("body").on("click", ".delete-coupon-code", function (e) {
      e.preventDefault();

      $.ajax({
        url: themeData.ajaxUrl,
        type: "POST",
        data: {
          action: "delete_coupon_code",
        },
        beforeSend: function () {
          $(".delete-coupon-code").css("opacity", "0.5");
        },
        success: function (res) {
          $(".delete-coupon-code").css("opacity", "1");

          if (res.success) {
            $(".coupon_code_response").html(
              '<p class="text-red-600">' + res.data.message + "</p>",
            );

            // Refresh checkout fragments
            $("body").trigger("update_checkout");
          }
        },
      });
    });
  });
}
