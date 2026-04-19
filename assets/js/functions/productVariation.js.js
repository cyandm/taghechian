export function ProductVariation() {
  const $form = jQuery(".variations_form.cart");
  if (!$form.length) {
    console.log("ProductVariation: Variations form not found. Exiting.");
    return;
  }
  console.log("ProductVariation: Variations form found.");

  const $priceDisplay = jQuery("#product-price-display");
  const $stockWrapper = jQuery("#stock-status-wrapper");
  const $stockText = jQuery("#stock-status-text");

  const i18nBox = document.querySelector("#variation-i18n");
  const t = {
    outOfStock: i18nBox?.dataset.outOfStock || "ناموجود",
    onlyLeft: i18nBox?.dataset.onlyLeft || "فقط",
    itemsLeft: i18nBox?.dataset.itemsLeft || "عدد باقیمانده",
  };

  let initialPriceHtml = $priceDisplay.length ? $priceDisplay.html() : "";
  let initialStockHtml = $stockWrapper.length ? $stockWrapper.html() : "";
  console.log("ProductVariation: Initial price HTML:", initialPriceHtml);
  console.log("ProductVariation: Initial stock HTML:", initialStockHtml);

  function setSelectAndTrigger(attrName, value) {
    console.log(
      `setSelectAndTrigger called for attribute: ${attrName}, value: ${value}`,
    );
    const $sel = $form.find(`select[name="attribute_${attrName}"]`);
    if ($sel.length && value) {
      console.log(
        `Select element found for attribute ${attrName}. Setting value to ${value}`,
      );
      $sel.val(value);
      $sel.trigger("change");
      console.log(`Triggered 'change' event for select ${attrName}.`);
      // Use setTimeout to ensure the change event is fully processed before checking variations
      setTimeout(() => {
        console.log(
          "Triggering 'check_variations' from setSelectAndTrigger...",
        );
        $form.trigger("check_variations");
      }, 0);
    } else {
      console.log(
        `Select element not found or value is empty for attribute ${attrName}.`,
      );
    }
  }

  function setSelectValue(attrName, value) {
    console.log(
      `setSelectValue called for attribute: ${attrName}, value: ${value}`,
    );
    const $sel = $form.find(`select[name="attribute_${attrName}"]`);
    if ($sel.length && value) {
      console.log(
        `Select element found for attribute ${attrName}. Setting value to ${value} in setSelectValue.`,
      );
      $sel.val(value);
    } else {
      console.log(
        `Select element not found or value is empty for attribute ${attrName} in setSelectValue.`,
      );
    }
  }

  // COLOR
  jQuery(document).on("click", ".color-option", function () {
    console.log("Color option clicked.");
    const attr = this.dataset.attribute;
    const val = this.dataset.color;
    console.log(`Clicked color: attribute=${attr}, value=${val}`);
    if (!attr || !val) {
      console.log("Missing attribute or value on color option. Skipping.");
      return;
    }

    jQuery(".color-option").removeClass("active");
    jQuery(this).addClass("active");
    console.log("Removed 'active' from other color options, added to clicked.");

    setSelectAndTrigger(attr, val);
  });

  // SIZE
  jQuery(document).on("click", ".size-option", function () {
    console.log("Size option clicked.");
    const attr = this.dataset.attribute;
    const val = this.dataset.size;
    console.log(`Clicked size: attribute=${attr}, value=${val}`);
    if (!attr || !val) {
      console.log("Missing attribute or value on size option. Skipping.");
      return;
    }

    jQuery(".size-option").removeClass("active");
    jQuery(this).addClass("active");
    console.log("Removed 'active' from other size options, added to clicked.");

    setSelectAndTrigger(attr, val);
  });

  // FOUND VARIATION
  $form.on("found_variation", function (evt, variation) {
    console.log("Event 'found_variation' triggered.", variation);
    if (variation.price_html && $priceDisplay.length) {
      if (variation.is_in_stock) {
        $priceDisplay.html(variation.price_html);
        console.log("Updated price display with:", variation.price_html);
      } else {
        $priceDisplay.html(
          `<span class="text-[#dd4a4a] text-lg">${t.outOfStock}</span>`,
        );
        console.log("Product is out of stock. Updated price display.");
      }
    }

    if ($stockWrapper.length && $stockText.length) {
      if (!variation.is_in_stock) {
        $stockText.text(t.outOfStock);
        $stockWrapper.removeClass("hidden");
        console.log("Stock status: Out of stock.");
      } else if (
        variation.max_qty != null &&
        variation.max_qty > 0 &&
        variation.max_qty <= 4
      ) {
        $stockText.text(`${t.onlyLeft} ${variation.max_qty} ${t.itemsLeft}`);
        $stockWrapper.removeClass("hidden");
        console.log(`Stock status: Only ${variation.max_qty} left.`);
      } else {
        $stockWrapper.addClass("hidden");
        console.log("Stock status: In stock, no special message.");
      }
    }
  });

  // RESET VARIATION
  $form.on("hide_variation", function () {
    console.log("Event 'hide_variation' triggered.");
    if (initialPriceHtml && $priceDisplay.length) {
      $priceDisplay.html(initialPriceHtml);
      console.log("Reset price display to initial HTML.");
    }
    if (initialStockHtml && $stockWrapper.length) {
      $stockWrapper.html(initialStockHtml);
      console.log("Reset stock display to initial HTML.");
    }
    $stockWrapper.addClass("hidden");
  });


}
