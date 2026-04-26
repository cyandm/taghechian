<?php

/**
 * Cart Page
 *
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

use Cyan\Theme\Helpers\Icon;

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="woocommerce-cart-form-wrapper" dir="rtl">
	<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
		<?php do_action('woocommerce_before_cart_table'); ?>

		<!-- Cart Header -->
		<div class="bg-white">
			<h1 class="text-3xl font-normal font-[dinar] text-cynBlack pb-3 md:pb-6"><?php the_title(); ?></h1>

			<!-- Table Header -->
			<div class="hidden md:grid grid-cols-12 gap-4 px-12 py-4 bg-cynBgItem/30 rounded-xl text-xs md:text-base font-medium text-cynBlack">
				<div class="col-span-3 text-start"><?php _e('نام محصول', 'taghechain'); ?></div>
				<div class="col-span-2 text-start"><?php _e('قیمت', 'taghechain'); ?></div>
				<div class="col-span-5 text-start"><?php _e('تعداد', 'taghechain'); ?></div>
				<div class="col-span-2 text-end"><?php _e('جمع نهایی', 'taghechain'); ?></div>
			</div>

			<!-- Cart Items -->
			<div class="cart-items-container flex flex-col max-md:gap-3">
				<?php
				foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
					$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
					$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

					if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
						$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
				?>

						<!-- DESKTOP CARD -->
						<div class="cart-item hidden md:grid grid-cols-4 md:grid-cols-12 gap-4 px-6 py-6 border-b border-cynBlack/10 items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">

							<!-- Product Image & Name (6 columns) -->
							<div class="col-span-4 md:col-span-3 flex items-center gap-4">
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
							<div class="col-span-4 md:col-span-2">
								<span class="text-sm font-medium text-gray-700">
									<?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
								</span>
							</div>

							<!-- Quantity Controls (4 columns) -->
							<div class="col-span-4 md:col-span-5 flex items-center gap-3">

								<div class="bg-cynBgItem/30 rounded-lg flex justify-center items-center gap-6 px-6 py-2">

									<div class="flex items-center overflow-hidden">

										<!-- Decrease Button -->
										<button type="button" class="quantity-btn quantity-plus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
											<?php Icon::Print('plus'); ?>
										</button>

										<!-- Quantity Input -->
										<?php
										echo woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '1',
												'product_name' => $_product->get_name(),
												'classes'      => array('input-text', 'product-quantity', 'text', '!border-0', '!border-none', '!outline-none', 'focus:outline-none', 'text-center', 'bg-cynBgItem/30', 'w-6', 'h-6', 'md:!w-8', 'md:!h-8', 'md:!min-h-8', 'focus:outline-none', 'focus:ring-0', 'text-xs', 'font-normal'),
											),
											$_product,
											false
										);
										?>

										<!-- Increase Button -->
										<button type="button" class="quantity-btn quantity-minus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
											<?php Icon::Print('minus'); ?>
										</button>

									</div>

									<!-- Remove Button -->
									<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
										class="text-[#C315A5] hover:text-pink-500 transition-colors"
										aria-label="<?php echo esc_attr(sprintf(__('حذف %s از سبد خرید', 'woocommerce'), $_product->get_name())); ?>"
										data-product_id="<?php echo esc_attr($_product->get_id()); ?>"
										data-product_sku="<?php echo esc_attr($_product->get_sku()); ?>">
										<div class="size-5 stroke-[1.5]">
											<?php Icon::print('trash-delete-bin-2-1'); ?>
										</div>
									</a>
								</div>
							</div>

							<!-- Subtotal (2 columns) -->
							<div class="col-span-4 md:col-span-2 text-end">
								<span class="text-base font-normal text-cynBlack item-subtotal">
									<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
								</span>
							</div>

						</div>

						<!-- MOBILE CARD -->
						<div class="cart-item md:hidden border border-cynBlack/10 rounded-2xl overflow-hidden text-sm" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">

							<!-- Product -->
							<div class=" flex items-center gap-5 border-b border-cynBlack/10">

								<div class="w-3/12 bg-cynBgItem/30 px-3 py-7 border-e border-cynBlack/10">
									<span class="text-cynBlack/70">محصول</span>
								</div>

								<div class="flex items-center gap-3 w-8/12">

									<div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0">
										<?php
										$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key);
										if (!$product_permalink) {
											echo $thumbnail;
										} else {
											printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
										}
										?>
									</div>

									<div class="text-right text-cynBlack text-xs font-medium">
										<?php
										if (!$product_permalink) {
											echo wp_kses_post($_product->get_name());
										} else {
											echo '<a href="' . esc_url($product_permalink) . '">' . wp_kses_post($_product->get_name()) . '</a>';
										}
										?>
									</div>

								</div>
							</div>


							<!-- Price -->
							<div class="flex items-center gap-5 border-b border-cynBlack/10">

								<div class="w-3/12 bg-cynBgItem/30 px-3 py-5 border-e border-cynBlack/10">
									<span class="text-cynBlack/70">قیمت</span>
								</div>

								<span class="text-cynBlack font-medium w-8/12">
									<?php echo WC()->cart->get_product_price($_product); ?>
								</span>

							</div>


							<!-- Quantity -->
							<div class="flex items-center border-b border-cynBlack/10">

								<div class="w-3/12 bg-cynBgItem/30 px-3 py-7 border-e border-cynBlack/10">
									<span class="text-cynBlack/70">تعداد</span>
								</div>

								<div class="flex items-center justify-between px-2 py-3 flex-1">

									<div class="flex items-center justify-between gap-4 bg-cynBgItem/30 rounded-lg px-4 py-2 w-full">

										<div class="flex items-center gap-1">

											<!-- Decrease Button -->
											<button type="button" class="quantity-btn quantity-plus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
												<?php Icon::Print('plus'); ?>
											</button>

											<!-- Quantity Input -->
											<?php
											echo woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $_product->get_max_purchase_quantity(),
													'min_value'    => '1',
													'product_name' => $_product->get_name(),
													'classes'      => array('input-text', 'product-quantity', 'text', '!border-0', '!border-none', '!outline-none', 'focus:outline-none', 'text-center', 'bg-cynBgItem/30', 'w-6', 'h-6', 'md:!w-8', 'md:!h-8', 'md:!min-h-8', 'focus:outline-none', 'focus:ring-0', 'text-xs', 'font-normal'),
												),
												$_product,
												false
											);
											?>

											<!-- Increase Button -->
											<button type="button" class="quantity-btn quantity-minus p-1 md:p-2 bg-white border border-cynBlack/10 rounded-md hover:bg-cynYellow text-cynBlack font-bold transition-colors size-6 md:size-8 flex justify-center items-center" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
												<?php Icon::Print('minus'); ?>
											</button>

										</div>

										<!-- remove -->
										<a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>"
											class="text-[#C315A5]">
											<div class="size-5 stroke-[1.5]">
												<?php Icon::print('trash-delete-bin-2-1'); ?>
											</div>
										</a>

									</div>

								</div>

							</div>


							<!-- Subtotal -->
							<div class="flex items-center gap-5">

								<div class="w-3/12 bg-cynBgItem/30 px-3 py-5 border-e border-cynBlack/10">
									<span class="text-cynBlack/70">جمع نهایی</span>
								</div>

								<span class="text-cynBlack text-base font-medium item-subtotal w-8/12">
									<?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
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
	<div class="cart-collaterals rounded-3xl border border-cynBlack/10 mt-6 p-6">
		<?php do_action('woocommerce_before_cart_collaterals'); ?>

		<div class="cart-totals-wrapper">
			<p class="text-2xl font-[dinar] text-cynBlack text-start mb-6"><?php _e('جمع کل سبد خرید', 'taghechain'); ?></p>

			<!-- Subtotal -->
			<div class="flex justify-between items-center py-3 px-2">
				<span class="text-cynBlack/80 font-medium text-base md:text-xl"><?php _e('مجموع سبد خرید', 'taghechain'); ?></span>
				<span class="text-sm font-medium text-cynBlack/80 cart-subtotal-amount"><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>

			<?php
			$total_saving = \Cyan\Theme\Classes\WooCommerce::cyn_get_cart_special_price_saving();

			if ($total_saving > 0) :
			?>

				<div class="h-px w-full bg-cynBlack/10 my-3"></div>

				<div class="flex justify-between items-center py-3 px-2">
					<span class="text-cynBlack/80 font-medium text-base md:text-xl"><?php _e('سود شما از این خرید', 'taghechain'); ?></span>
					<span class="text-[#BE123C] text-sm font-medium cart-saving-amount"><?php echo wc_price($total_saving); ?></span>
				</div>
			<?php endif; ?>

			<div class="h-px w-full bg-cynBlack/10 my-3"></div>

			<!-- Total -->
			<div class="flex justify-between items-center py-4 mt-2 px-2">
				<span class="font-medium text-base md:text-xl text-cynBlack/80"><?php _e('قابل پرداخت', 'taghechain'); ?></span>
				<span class="text-base md:text-xl font-medium text-cynBlue cart-total-amount"><?php wc_cart_totals_order_total_html(); ?></span>
			</div>

		</div>

		<?php do_action('woocommerce_after_cart_totals'); ?>
	</div>

	<!-- Proceed to Checkout Button -->
	<div class="mt-6 flex md:justify-end">
		<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="primary-btn rounded-4xl transition-colors font-medium text-base max-md:w-full text-center">
			<?php _e('تایید و تکمیل سفارش', 'taghechain'); ?>
		</a>
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
</style>