<?php

/**
 * Edit address form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

$page_title = ('billing' === $load_address) ? esc_html__('Billing address', 'woocommerce') : esc_html__('Shipping address', 'woocommerce');
$account_base_url = wc_get_page_permalink('myaccount');
$address_index_url = wc_get_endpoint_url('edit-address', '', $account_base_url);

do_action('woocommerce_before_edit_account_address_form'); ?>

<?php if (! $load_address) : ?>
	<?php wc_get_template('myaccount/my-address.php'); ?>
<?php else : ?>

	<div class="bg-white rounded-2xl border border-gray-200 p-6">
		<div class="mb-6">
			<h2 class="text-xl font-[dinar] font-medium text-gray-900 mb-2">
				<?php echo esc_html(apply_filters('woocommerce_my_account_edit_address_title', $page_title, $load_address)); ?>
			</h2>
			<p class="text-sm text-gray-500">
				<?php esc_html_e('Please complete the fields below to update your address details.', 'woocommerce'); ?>
			</p>
		</div>

		<form method="post" novalidate>

			<div class="woocommerce-address-fields">
				<?php do_action("woocommerce_before_edit_address_form_{$load_address}"); ?>

				<div class="woocommerce-address-fields__field-wrapper grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-right">
					<?php
					$full_width_fields = array(
						'billing_company',
						'shipping_company',
						'billing_address_1',
						'shipping_address_1',
						'billing_address_2',
						'shipping_address_2',
					);

					foreach ($address as $key => $field) {
						$field_classes = isset($field['class']) && is_array($field['class']) ? $field['class'] : array();
						$field_classes[] = in_array($key, $full_width_fields, true) ? 'md:col-span-2' : 'md:col-span-1';
						$field['class'] = $field_classes;

						$input_classes = isset($field['input_class']) && is_array($field['input_class']) ? $field['input_class'] : array();
						$input_classes = array_merge(
							$input_classes,
							array(
								'w-full',
								'px-4',
								'py-3',
								'border',
								'border-gray-200',
								'rounded-xl',
								'focus:outline-none',
								'focus:ring-2',
								'focus:ring-yellow-400',
								'text-right',
								'text-gray-900',
							)
						);
						$field['input_class'] = array_values(array_unique($input_classes));

						$label_classes = isset($field['label_class']) && is_array($field['label_class']) ? $field['label_class'] : array();
						$label_classes = array_merge($label_classes, array('block', 'mb-2', 'text-sm', 'text-gray-600'));
						$field['label_class'] = array_values(array_unique($label_classes));

						woocommerce_form_field($key, $field, wc_get_post_data_by_key($key, $field['value']));
					}
					?>
				</div>

				<?php do_action("woocommerce_after_edit_address_form_{$load_address}"); ?>

				<div class="flex justify-end gap-3">
					<a href="<?php echo esc_url($address_index_url); ?>"
						class="px-8 py-3 border-2 border-yellow-400 text-gray-900 font-medium rounded-xl hover:bg-yellow-50 transition">
						<?php esc_html_e('Cancel', 'woocommerce'); ?>
					</a>
					<button type="submit"
						class="px-8 py-3 bg-yellow-400 text-gray-900 font-medium rounded-xl hover:bg-yellow-500 transition"
						name="save_address" value="<?php esc_attr_e('Save address', 'woocommerce'); ?>">
						<?php esc_html_e('Save address', 'woocommerce'); ?>
					</button>
					<?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
					<input type="hidden" name="action" value="edit_address" />
				</div>
			</div>
		</form>
	</div>
<?php endif; ?>

<?php do_action('woocommerce_after_edit_account_address_form'); ?>