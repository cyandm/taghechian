<?php

/**
 * Cart Page
 *
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="woocommerce-cart-form-wrapper" dir="rtl">
	<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
		<?php do_action('woocommerce_before_cart_table'); ?>

		<!-- Cart Header -->
		<div class="bg-white">
			<h1 class="text-3xl font-normal font-[dinar] text-cynBlack pb-3 md:pb-6">سبد خرید</h1>

			<!-- Table Header -->
			<div class="hidden md:grid grid-cols-12 gap-4 px-12 py-4 bg-cynBgItem/30 rounded-xl text-xs md:text-base font-medium text-cynBlack">
				<div class="col-span-2 text-right">نام محصول</div>
				<div class="col-span-2 text-center">قیمت</div>
				<div class="col-span-6 text-center">تعداد</div>
				<div class="col-span-2 text-right">جمع نهایی</div>
			</div>

			<!-- Cart Items -->
			<div class="cart-items-container">
				<?php
				foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
					$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
					$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

					if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
						$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
				?>
						<div class="cart-item grid grid-cols-12 gap-4 px-6 py-6 border-b border-gray-100 hover:bg-gray-50 transition-colors items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">

							<!-- Product Image & Name (6 columns) -->
							<div class="col-span-12 md:col-span-2 flex items-center gap-4">
								<!-- Product Image -->
								<div class="flex-shrink-0">
									<?php
									$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
									if (!$product_permalink) {
										echo $thumbnail;
									} else {
										printf('<a href="%s" class="block w-12 h-12 rounded-lg overflow-hidden">%s</a>', esc_url($product_permalink), $thumbnail);
									}
									?>
								</div>

								<!-- Product Name & Meta -->
								<div class="flex-1 font-medium text-xs text-cynBlack">
									<?php
									if (!$product_permalink) {
										echo '<h3>' . wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)) . '</h3>';
									} else {
										echo '<a href="' . esc_url($product_permalink) . '" class="hover:text-blue-600 transition-colors">' . wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)) . '</a>';
									}

									// Meta data
									echo wc_get_formatted_cart_item_data($cart_item);

									// Backorder notification
									if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
										echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification text-xs text-amber-600 mt-1">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
									}
									?>
									<!-- SKU -->
									<?php if ($_product->get_sku()) : ?>
										<p class="mt-1"><?php echo esc_html($_product->get_sku()); ?></p>
									<?php endif; ?>
								</div>
							</div>

							<!-- Price (2 columns) -->
							<div class="col-span-4 md:col-span-2 text-center">
								<span class="text-sm md:text-base font-medium text-gray-700">
									<?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
								</span>
							</div>

							<!-- Quantity Controls (4 columns) -->
							<div class="col-span-4 md:col-span-2 flex items-center justify-center gap-3">
								<div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
									<!-- Decrease Button -->
									<button type="button" class="quantity-btn quantity-minus px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
										−
									</button>

									<!-- Quantity Input -->
									<?php
									echo woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $_product->get_max_purchase_quantity(),
											'min_value'    => '0',
											'product_name' => $_product->get_name(),
											'classes'      => array('input-text', 'qty', 'text', 'border-0', 'text-center', 'w-16', 'focus:outline-none', 'focus:ring-0'),
										),
										$_product,
										false
									);
									?>

									<!-- Increase Button -->
									<button type="button" class="quantity-btn quantity-plus px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
										+
									</button>
								</div>

								<!-- Remove Button -->
								<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
									class="text-pink-500 hover:text-pink-600 transition-colors remove-item"
									aria-label="<?php echo esc_attr(sprintf(__('حذف %s از سبد خرید', 'woocommerce'), $_product->get_name())); ?>"
									data-product_id="<?php echo esc_attr($_product->get_id()); ?>"
									data-product_sku="<?php echo esc_attr($_product->get_sku()); ?>">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
									</svg>
								</a>
							</div>

							<!-- Subtotal (2 columns) -->
							<div class="col-span-4 md:col-span-2 text-center">
								<span class="text-sm md:text-base font-bold text-gray-800 item-subtotal">
									<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
								</span>
							</div>

						</div>
				<?php
					}
				}
				?>
			</div>
		</div>

		<?php do_action('woocommerce_cart_contents'); ?>
		<?php do_action('woocommerce_after_cart_contents'); ?>
		<?php do_action('woocommerce_after_cart_table'); ?>
	</form>

	<!-- Cart Totals Section -->
	<div class="cart-collaterals bg-white rounded-b-2xl shadow-sm mt-6 p-6">
		<?php do_action('woocommerce_before_cart_collaterals'); ?>

		<div class="cart-totals-wrapper">
			<h2 class="text-2xl font-bold text-gray-800 text-right mb-6">جمع کل سبد خرید</h2>

			<!-- Subtotal -->
			<div class="flex justify-between items-center py-3 border-b border-gray-200">
				<span class="text-gray-600">مجموع سبد خرید</span>
				<span class="font-semibold text-gray-800 cart-subtotal-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>

			<!-- Total -->
			<div class="flex justify-between items-center py-4 mt-2">
				<span class="text-lg font-bold text-gray-800">قابل پرداخت</span>
				<span class="text-2xl font-bold text-blue-600 cart-total-amount"><?php wc_cart_totals_order_total_html(); ?></span>
			</div>

			<!-- Proceed to Checkout Button -->
			<div class="mt-6">
				<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="block w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 text-center font-bold py-4 px-6 rounded-xl transition-colors text-lg">
					تایید و تکمیل سفارش
				</a>
			</div>

			<!-- Coupon Form -->
			<?php if (wc_coupons_enabled()) : ?>
				<div class="coupon-section mt-6 flex gap-2">
					<input type="text" name="coupon_code" class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-right focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="کد تخفیف" id="coupon_code" value="" />
					<button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-3 rounded-lg transition-colors whitespace-nowrap" name="apply_coupon" value="اعمال کوپن">اعمال کوپن</button>
				</div>
			<?php endif; ?>

			<!-- Update Cart Button -->
			<div class="mt-4">
				<button type="button" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors" name="update_cart" value="بروزرسانی سبد خرید" disabled>
					بروزرسانی سبد خرید
				</button>
			</div>
		</div>

		<?php do_action('woocommerce_after_cart_totals'); ?>
	</div>
</div>

<?php do_action('woocommerce_after_cart'); ?>

<style>
	.cart-item.loading {
		opacity: 0.6;
		pointer-events: none;
	}

	.cart-item.loading::after {
		content: '';
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		width: 24px;
		height: 24px;
		border: 3px solid #f3f4f6;
		border-top-color: #3b82f6;
		border-radius: 50%;
		animation: spin 0.6s linear infinite;
	}

	@keyframes spin {
		to {
			transform: translate(-50%, -50%) rotate(360deg);
		}
	}

	/* Mobile Responsive Styles */
	@media (max-width: 768px) {
		.cart-item {
			display: flex;
			flex-direction: column;
			gap: 1rem;
			position: relative;
		}

		/* Product Image & Name - Full Width */
		.cart-item>div:nth-child(1) {
			order: 1;
			grid-column: span 12;
		}

		/* Price */
		.cart-item>div:nth-child(2) {
			order: 2;
			grid-column: span 12;
			text-align: right !important;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.cart-item>div:nth-child(2)::before {
			content: 'قیمت:';
			font-weight: 600;
			color: #4b5563;
		}

		/* Quantity Controls */
		.cart-item>div:nth-child(3) {
			order: 3;
			grid-column: span 12;
			justify-content: space-between !important;
		}

		.cart-item>div:nth-child(3)::before {
			content: 'تعداد:';
			font-weight: 600;
			color: #4b5563;
		}

		/* Subtotal */
		.cart-item>div:nth-child(4) {
			order: 4;
			grid-column: span 12;
			text-align: right !important;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.cart-item>div:nth-child(4)::before {
			content: 'جمع نهایی:';
			font-weight: 600;
			color: #4b5563;
		}

		/* Remove button positioning */
		.cart-item .remove-item {
			position: absolute;
			top: 1rem;
			left: 1rem;
		}
	}
</style>