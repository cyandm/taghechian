/* productVariation.js
 * Smooth price and stock status animation for WooCommerce variable products
 * Requirements:
 * - jQuery (bundled with WooCommerce)
 * - TailwindCSS for opacity / translate / transition classes (optional but recommended)
 */

export function ProductVariation() {
  const $form = jQuery(".variations_form.cart");
  if (!$form.length) return;

  // Elements
  const $priceDisplay = jQuery("#product-price-display");
  const $stockWrapper = jQuery("#stock-status-wrapper");
  const $stockText = jQuery("#stock-status-text");

  // i18n values from DOM (fallback defaults)
  const i18nBox = document.querySelector("#variation-i18n");
  const t = {
    outOfStock: i18nBox?.dataset.outOfStock || "ناموجود",
    onlyLeft: i18nBox?.dataset.onlyLeft || "فقط",
    itemsLeft: i18nBox?.dataset.itemsLeft || "عدد باقیمانده",
  };

  const initialPriceHtml = $priceDisplay.length ? $priceDisplay.html() : "";

  // Ensure the stock wrapper is animatable
  if ($stockWrapper.length) {
    if ($stockWrapper.hasClass("hidden")) {
      $stockWrapper
        .removeClass("hidden")
        .addClass("opacity-0 -translate-y-1 pointer-events-none");
    }

    $stockWrapper.addClass("transition-all duration-300 ease-out");
  }

  // Animate price change
  function animatePrice(newHtml, inStock) {
    if (!$priceDisplay.length) return;

    const newContent = inStock
      ? newHtml
      : `<span class="text-[#dd4a4a] text-lg">${t.outOfStock}</span>`;

    $priceDisplay.addClass("transition-opacity duration-300");
    $priceDisplay.removeClass("opacity-100").addClass("opacity-0");

    setTimeout(() => {
      $priceDisplay.html(newContent);
      $priceDisplay.removeClass("opacity-0").addClass("opacity-100");
    }, 180);
  }

  // Show stock message with fade animation (similar to price)
  function showStock(text) {
    if (!$stockWrapper.length || !$stockText.length) return;

    // If already visible, perform fade-out/fade-in transition
    if ($stockWrapper.hasClass("opacity-100") && $stockText.text() !== text) {
      $stockWrapper.removeClass("opacity-100").addClass("opacity-0");

      setTimeout(() => {
        $stockText.text(text);
        $stockWrapper.removeClass("opacity-0").addClass("opacity-100");
      }, 180); // Matches the price animation delay
    } else {
      // If hidden or text is the same, show/update normally
      $stockText.text(text);
      $stockWrapper
        .removeClass("opacity-0 -translate-y-1 pointer-events-none")
        .addClass("opacity-100 translate-y-0");
    }
  }

  // Hide stock message
  function hideStock() {
    if (!$stockWrapper.length) return;

    $stockWrapper
      .removeClass("opacity-100 translate-y-0")
      .addClass("opacity-0 -translate-y-1 pointer-events-none");
  }

  // Update select value and trigger WooCommerce variation check
  function setSelectAndTrigger(attrName, value) {
    const $sel = $form.find(`select[name="attribute_${attrName}"]`);

    if ($sel.length && value) {
      $sel.val(value);
      $sel.trigger("change");

      setTimeout(() => {
        $form.trigger("check_variations");
      }, 0);
    }
  }

  // Set select value without triggering events
  function setSelectValue(attrName, value) {
    const $sel = $form.find(`select[name="attribute_${attrName}"]`);

    if ($sel.length && value) {
      $sel.val(value);
    }
  }

  // Custom color option handler
  jQuery(document).on("click", ".color-option", function () {
    const attr = this.dataset.attribute;
    const val = this.dataset.color;

    if (!attr || !val) return;

    jQuery(".color-option").removeClass("active");
    jQuery(this).addClass("active");

    setSelectAndTrigger(attr, val);
  });

  // Custom size option handler
  jQuery(document).on("click", ".size-option", function () {
    const attr = this.dataset.attribute;
    const val = this.dataset.size;

    if (!attr || !val) return;

    jQuery(".size-option").removeClass("active");
    jQuery(this).addClass("active");

    setSelectAndTrigger(attr, val);
  });

  // WooCommerce event: variation found
  $form.on("found_variation", function (evt, variation) {
    if (variation.price_html) {
      animatePrice(variation.price_html, variation.is_in_stock);
    }

    if (!variation.is_in_stock) {
      showStock(t.outOfStock);
    } else if (
      variation.max_qty != null &&
      variation.max_qty > 0 &&
      variation.max_qty <= 4
    ) {
      showStock(`${t.onlyLeft} ${variation.max_qty} ${t.itemsLeft}`);
    } else {
      hideStock();
    }
  });

  // WooCommerce event: variation reset or not found
  $form.on("hide_variation", function () {
    if (initialPriceHtml && $priceDisplay.length) {
      animatePrice(initialPriceHtml, true);
    }

    hideStock();
  });

  // Set default selections on initial load
  setTimeout(() => {
    let selectsReady = false;

    const $color = jQuery(".color-option").first();
    if (
      $color.length &&
      $color[0].dataset.attribute &&
      $color[0].dataset.color
    ) {
      $color.addClass("active");
      setSelectValue($color[0].dataset.attribute, $color[0].dataset.color);
      selectsReady = true;
    }

    const $size = jQuery(".size-option").first();
    if ($size.length && $size[0].dataset.attribute && $size[0].dataset.size) {
      $size.addClass("active");
      setSelectValue($size[0].dataset.attribute, $size[0].dataset.size);
      selectsReady = true;
    }

    if (selectsReady) {
      $form.trigger("check_variations");
    }
  }, 200);
}
