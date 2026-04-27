<?php

/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

$customer_id = get_current_user_id();

if (! wc_ship_to_billing_address_only() && wc_shipping_enabled()) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __('Billing address', 'woocommerce'),
			'shipping' => __('Shipping address', 'woocommerce'),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __('Billing address', 'woocommerce'),
		),
		$customer_id
	);
}

?>

<div class="bg-white rounded-2xl border border-gray-200 p-6">
	<p class="text-sm text-gray-500 mb-6">
		<?php echo apply_filters('woocommerce_my_account_my_address_description', esc_html__('The following addresses will be used on the checkout page by default.', 'woocommerce')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
		?>
	</p>

	<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
		<?php foreach ($get_addresses as $name => $address_title) : ?>
			<?php $address = wc_get_account_formatted_address($name); ?>
			<div class="border border-gray-200 rounded-xl p-4">
				<header class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100">
					<h2 class="text-base font-[dinar] font-medium text-gray-900"><?php echo esc_html($address_title); ?></h2>
					<a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $name)); ?>"
						class="text-blue-600 flex items-center gap-1 text-sm">
						<?php
						printf(
							/* translators: %s: Address title */
							$address ? esc_html__('Edit %s', 'woocommerce') : esc_html__('Add %s', 'woocommerce'),
							esc_html($address_title)
						);
						?>
					</a>
				</header>
				<address class="not-italic text-gray-700 leading-7">
					<?php
					if ($address) {
						echo wp_kses_post($address);
					} else {
						echo esc_html__('You have not set up this type of address yet.', 'woocommerce');
					}

					/**
					 * Used to output content after core address fields.
					 *
					 * @param string $name Address type.
					 * @since 8.7.0
					 */
					do_action('woocommerce_my_account_after_my_address', $name);
					?>
				</address>
			</div>
		<?php endforeach; ?>
	</div>
</div>