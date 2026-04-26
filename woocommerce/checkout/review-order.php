<?php

use Cyan\Theme\Helpers\Icon;

defined('ABSPATH') || exit;

?>

<div class="flex flex-col gap-3 woocommerce-checkout-review-order-table">

	<div class="flex flex-col gap-4 text-sm border border-cynBlack/10 rounded-2xl p-4 md:p-6">

		<p class="text-2xl font-[dinar] mb-4">
			جمع کل سبد خرید
		</p>

		<!-- آیتم‌های سبد -->
		<!-- <div class="flex flex-col gap-3">

		<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
			$_product = $cart_item['data'];

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0) :
		?>
				<div class="flex justify-between items-center border-b border-cynBlack/10 pb-2">
					<div class="flex flex-col gap-1">
						<span class="font-medium">
							<?php echo $_product->get_name(); ?>
						</span>
						<span class="text-xs text-gray-500">
							× <?php echo $cart_item['quantity']; ?>
						</span>
					</div>

					<div class="font-medium">
						<?php echo WC()->cart->get_product_subtotal($_product, $cart_item['quantity']); ?>
					</div>
				</div>
		<?php
			endif;
		endforeach; ?>
	</div> -->

		<!-- جمع جزء -->
		<div class="flex justify-between pt-2">
			<span class="text-base md:text-xl font-medium text-cynBlack/80"><?php _e('مجموع سبد خرید', 'taghechian') ?></span>
			<div class="flex gap-1.5 flex-wrap">
				<span class="text-sm font-medium text-cynBlack/80 flex items-center">
					<?php wc_cart_totals_subtotal_html(); ?>
				</span>

				<a href="<?= wc_get_cart_url(); ?>" class="font-medium text-xs flex items-center text-cynBlue/80">
					<?php _e('مشاهده سبد خرید', 'taghechian') ?>
					<i class="size-5">
						<?php Icon::print('Arrow-27'); ?>
					</i>
				</a>

			</div>
		</div>

		<div class="h-px w-full bg-cynBlack/10 my-1"></div>

		<!-- حمل و نقل -->
		<?php if (WC()->cart->needs_shipping()) : ?>
			<div class="flex justify-between flex-col gap-2">
				<span class="text-base md:text-xl !font-medium text-cynBlack/80"><?php _e('حمل و نقل', 'taghechian') ?></span>
				<span class="text-base !font-medium text-cynBlack/80"><?php wc_cart_totals_shipping_html(); ?></span>
			</div>
		<?php endif; ?>

		<?php if (WC()->cart->get_discount_total() > 0): ?>
			<div class="h-px w-full bg-cynBlack/10 my-1"></div>

			<!-- مقدار تخفیف -->
			<div class="flex justify-between items-center">
				<div class="flex gap-2 items-center">

					<span class="text-lg !font-medium text-[#C11B44]">
						<?php _e('مقدار تخفیف', 'taghechian') ?>
					</span>

					<span class="text-[#C11B44] text-sm flex delete-coupon-code cursor-pointer">
						<i class="size-5">
							<?php Icon::print('trash-delete-bin-2-1'); ?>
						</i>
						<?php _e('حذف') ?>
					</span>

				</div>
				<span class="text-[#C11B44] text-xl [&_span]:!font-medium">
					<?php echo wc_price(WC()->cart->get_discount_total()); ?>
				</span>
			</div>

		<?php endif; ?>

		<div class="h-px w-full bg-cynBlack/10 my-1"></div>

		<!-- مبلغ نهایی -->
		<div class="flex justify-between items-center">
			<span class="text-xl !font-medium text-cynBlack/80"><?php _e('قابل پرداخت', 'taghechian') ?></span>
			<span class="text-cynBlue/80 text-2xl [&_span]:!font-medium">
				<?php wc_cart_totals_order_total_html(); ?>
			</span>
		</div>

	</div>

	<!-- کد تخفیف -->
	<div class="coupon_code_loader flex flex-col gap-4 border border-cynBlack/10 rounded-2xl p-4 md:p-6">

		<div class="flex items-center gap-2">

			<i class="text-cynYellow size-6">
				<?php Icon::print('Gift,-Box-1'); ?>
			</i>

			<span class="text-base md:text-xl font-medium text-cynBlack/80">
				<?php _e('کد تخفیف', 'taghechian') ?>
			</span>

		</div>

		<div class="flex gap-2">

			<input
				type="text"
				name="coupon_code"
				class="coupon_code flex-1 border border-gray-300 rounded-full px-5 py-3 text-sm"
				placeholder="<?php esc_attr_e('کد تخفیف خود را وارد کنید', 'taghechian'); ?>" />

			<button
				type="button"
				name="apply_coupon"
				value="1"
				class="apply-coupon-btn primary-btn rounded-full px-8 py-3">
				<?php _e('اعمال کد', 'taghechian'); ?>
			</button>

		</div>

		<div class="coupon_code_response text-base font-medium">

		</div>

	</div>

</div>