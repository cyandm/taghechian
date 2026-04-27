<?php

/**
 * My Account Dashboard – Content only (no outer container)
 */

if (! defined('ABSPATH')) {
	exit;
}

$current_user = wp_get_current_user();
$customer_id  = get_current_user_id();

$first_name = $current_user->first_name;
if (empty($first_name)) {
	$first_name = get_user_meta($customer_id, 'first_name', true);
}
if (empty($first_name)) {
	$first_name = get_user_meta($customer_id, 'billing_first_name', true);
}

$last_name = $current_user->last_name;
if (empty($last_name)) {
	$last_name = get_user_meta($customer_id, 'last_name', true);
}
if (empty($last_name)) {
	$last_name = get_user_meta($customer_id, 'billing_last_name', true);
}

$address_first_name = is_string($first_name) ? trim($first_name) : '';
$address_last_name  = is_string($last_name) ? trim($last_name) : '';
$address_email      = is_string($current_user->user_email) ? trim($current_user->user_email) : '';

if ($address_first_name === '') {
	$address_first_name = is_string($current_user->display_name) ? trim($current_user->display_name) : '';
}
$billing_phone = get_user_meta($customer_id, 'billing_phone', true);
$email         = $current_user->user_email;
$display_phone = ! empty($billing_phone) ? $billing_phone : '۰۹۱۳۳ ۹۶۷۳۵۴۰';

$account_balance = get_user_meta($customer_id, '_wallet_balance', true);
$balance_amount  = ! empty($account_balance) ? number_format($account_balance) : '۰';

$orders_url    = wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'));
$addresses_url = wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount'));
$account_url   = wc_get_page_permalink('myaccount');
$wallet_url    = '#'; // replace with your wallet endpoint
$logout_url    = wc_logout_url();

$billing_address_display  = wc_get_account_formatted_address('billing');
$shipping_address_display = wc_get_account_formatted_address('shipping');

$address_items = array(
	array(
		'key' => 'billing',
		'label' => 'آدرس صورتحساب',
		'display' => $billing_address_display,
		'edit_url' => add_query_arg('edit-address', 'billing', $addresses_url),
	),
	array(
		'key' => 'shipping',
		'label' => 'آدرس ارسال',
		'display' => $shipping_address_display,
		'edit_url' => add_query_arg('edit-address', 'shipping', $addresses_url),
	),
);

$customer = new WC_Customer($customer_id);
$full_width_address_fields = array(
	'billing_company',
	'shipping_company',
	'billing_address_1',
	'shipping_address_1',
	'billing_address_2',
	'shipping_address_2',
);

foreach ($address_items as &$address_item) {
	$address_type = $address_item['key'];
	$country_key = $address_type . '_country';
	$country_value = get_user_meta($customer_id, $country_key, true);

	if (! is_string($country_value) || $country_value === '') {
		$country_value = 'IR';
	}

	$address_fields = WC()->countries->get_address_fields($country_value, $address_type . '_');
	unset($address_fields[$country_key]);
	unset($address_fields[$address_type . '_first_name']);
	unset($address_fields[$address_type . '_last_name']);
	unset($address_fields[$address_type . '_email']);
	foreach ($address_fields as $field_key => &$field) {
		$field_classes = isset($field['class']) && is_array($field['class']) ? $field['class'] : array();
		$field_classes[] = in_array($field_key, $full_width_address_fields, true) ? 'md:col-span-2' : 'md:col-span-1';
		$field['class'] = array_values(array_unique($field_classes));

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

		$field['value'] = get_user_meta($customer_id, $field_key, true);
	}
	unset($field);

	$has_address = ! empty($address_item['display']) && $address_item['display'] !== '&nbsp;';
	$address_item['has_address'] = $has_address;
	$address_item['modal_name'] = 'account-address-modal-' . $address_type;
	$address_item['modal_title'] = $has_address ? 'ویرایش آدرس' : 'آدرس جدید';
	$address_item['fields'] = $address_fields;
}
unset($address_item);

$default_add_modal_name = 'account-address-modal-billing';
foreach ($address_items as $address_item) {
	if (! $address_item['has_address']) {
		$default_add_modal_name = $address_item['modal_name'];
		break;
	}
}
?>
<div class="space-y-6">
	<!-- Account Overview Card -->
	<div class="bg-white rounded-2xl border-1 border-gray-200 p-6">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-4">
				<div class="w-20 h-20 bg-yellow-400 rounded-full flex items-center justify-center">
					<div class="w-16 h-16 bg-yellow-500 rounded-full"></div>
				</div>
				<div class="text-left">
					<!--<p class="text-blue-600 text-sm mb-1">تغییر آواتار</p>-->
					<p class="text-2xl font-bold text-gray-900"><?php echo esc_html($display_phone); ?></p>
				</div>
			</div>
			<!--         <div class="text-right">
            <p class="text-gray-600 mb-2">موجودی حساب</p>
            <p class="text-2xl font-bold text-gray-900 mb-1"><?php echo esc_html($balance_amount); ?> <span
                    class="text-sm text-gray-500">تومان</span></p>
            <a href="<?php echo esc_url($wallet_url); ?>" class="text-blue-600 text-sm flex items-center gap-1">
                <span>افزایش موجودی</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        </div> -->
		</div>
	</div>

	<!-- User Info Card -->
	<div class="bg-white rounded-2xl border border-gray-200 p-6">
		<h2 class="text-xl font-medium text-gray-900 font-[dinar] mb-6 text-right">اطلاعات حساب کاربری</h2>

		<div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100">
			<p class="text-gray-500 text-sm">شماره موبایل</p>

			<div class="text-right">
				<p class="font-semibold text-gray-900"><?php echo esc_html($display_phone); ?></p>
			</div>

			<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-600 rounded-full text-sm">
				<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd"
						d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
						clip-rule="evenodd" />
				</svg>
				تایید شده
			</span>

			<!-- <a href="<?php echo esc_url($account_url); ?>" class="text-blue-500 flex items-center gap-1 text-sm">
                ویرایش
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </a> -->
		</div>

		<!--         <div class="flex items-center justify-between">
            <p class="text-gray-500 text-sm">ایمیل</p>

            <div class="text-center flex-1">
                <p class="font-semibold text-gray-900">___</p>
            </div>

            <a href="#" class="text-blue-500 flex items-center gap-1 text-sm">
                افزودن
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </div> -->
	</div>



	<!-- Secondary Info Card (Edit Account Form) -->
	<form method="post" class="bg-white rounded-2xl border-1 border-gray-200 p-6"
		action="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>">
		<h2 class="text-xl font-medium font-[dinar] text-gray-900 mb-6">اطلاعات ثانویه</h2>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
			<input type="text" name="account_first_name" placeholder="نام" value="<?php echo esc_attr($first_name); ?>"
				class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 text-right text-gray-900" />
			<input type="text" name="account_last_name" placeholder="نام خانوادگی"
				value="<?php echo esc_attr($last_name); ?>"
				class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 text-right text-gray-900" />
		</div>
		<!--  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="relative">
                <select name="gender"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 text-right appearance-none text-gray-900">
                    <option value="">جنسیت</option>
                    <option value="male">مرد</option>
                    <option value="female">زن</option>
                </select>
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="relative">
                <input type="text" name="birthdate" placeholder="تاریخ تولد"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-400 text-right text-gray-900" />
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div> -->
		<div class="flex justify-end gap-3">
			<button type="submit"
				class="px-10 py-2 bg-yellow-400 text-gray-900 font-medium rounded-full hover:bg-yellow-500 transition">تایید</button>
			<a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>"
				class="px-10 py-2 border-2 border-yellow-400 text-gray-900 font-medium rounded-full hover:bg-yellow-50 transition">انصراف</a>
		</div>
		<input type="hidden" name="action" value="save_account_details" />
		<input type="hidden" name="account_display_name" value="<?php echo esc_attr($current_user->display_name); ?>" />
		<input type="hidden" name="account_email" value="<?php echo esc_attr($email); ?>" />
		<input type="hidden" name="cyn_account_form_source" value="dashboard_names" />
		<?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
	</form>

	<!-- Addresses Card -->
	<div class="bg-white rounded-2xl border-1 border-gray-200 p-6">
		<div class="flex items-center justify-between mb-6">
			<h2 class="text-xl font-[dinar] font-medium text-gray-900">آدرس ها</h2>

			<!--             <button type="button" modal-opener data-modal-name="<?php echo esc_attr($default_add_modal_name); ?>"
                class="text-blue-600 flex items-center gap-1 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                افزودن
            </button> -->
		</div>
		<div class="space-y-3 mb-6">
			<?php foreach ($address_items as $address_item) : ?>
				<div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
					<div class="flex gap-2">
						<button type="button" modal-opener
							data-modal-name="<?php echo esc_attr($address_item['modal_name']); ?>"
							class="p-2 hover:bg-gray-50 rounded-lg">
							<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
							</svg>
						</button>
					</div>
					<div class="text-right">
						<p class="text-sm text-gray-500 mb-1"><?php echo esc_html($address_item['label']); ?></p>
						<?php if ($address_item['has_address']): ?>
							<p class="text-gray-900"><?php echo wp_kses_post($address_item['display']); ?>
							</p>
						<?php else: ?>
							<p class="text-gray-400">هنوز ثبت نشده است</p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="flex justify-end gap-3">
			<button type="button"
				class="px-10 py-2 bg-yellow-400 text-gray-900 font-medium rounded-full hover:bg-yellow-500 transition">تایید</button>
			<a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>"
				class="px-10 py-2 border-2 border-yellow-400 text-gray-900 font-medium rounded-full hover:bg-yellow-50 transition">انصراف</a>
		</div>
	</div>
</div>
<?php foreach ($address_items as $address_item) : ?>
	<div class="container cyn-address-modal flex justify-center items-center fixed inset-0 z-50 opacity-0 pointer-events-none w-full data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500"
		modal data-modal-name="<?php echo esc_attr($address_item['modal_name']); ?>" data-active="false"
		data-modal-discard-on-close="true">
		<div class="cyn-address-modal__panel w-full bg-cynWhite border border-cynBlack/10 shadow-item">
			<div class="cyn-address-modal__header">
				<p class="cyn-address-modal__title">
					<?php echo esc_html($address_item['modal_title']); ?>
				</p>
			</div>
			<form class="cyn-address-modal__form" method="post" action="<?php echo esc_url($address_item['edit_url']); ?>"
				novalidate>
				<div class="woocommerce-address-fields cyn-address-modal__fields-wrap">
					<div class="woocommerce-address-fields__field-wrapper cyn-address-modal__fields">
						<?php foreach ($address_item['fields'] as $field_key => $field) : ?>
							<?php woocommerce_form_field($field_key, $field, wc_get_post_data_by_key($field_key, $field['value'])); ?>
						<?php endforeach; ?>
					</div>
					<div class="cyn-address-modal__actions">
						<button type="submit" class="cyn-address-modal__btn cyn-address-modal__btn--primary">
							تایید
						</button>
						<button type="button" modal-closer
							data-modal-name="<?php echo esc_attr($address_item['modal_name']); ?>"
							class="cyn-address-modal__btn cyn-address-modal__btn--secondary">
							انصراف
						</button>
					</div>
					<input type="hidden" name="action" value="edit_address" />
					<input type="hidden" name="<?php echo esc_attr($address_item['key'] . '_first_name'); ?>"
						value="<?php echo esc_attr($address_first_name); ?>" />
					<input type="hidden" name="<?php echo esc_attr($address_item['key'] . '_last_name'); ?>"
						value="<?php echo esc_attr($address_last_name); ?>" />
					<input type="hidden" name="<?php echo esc_attr($address_item['key'] . '_email'); ?>"
						value="<?php echo esc_attr($address_email); ?>" />
					<input type="hidden" name="<?php echo esc_attr($address_item['key'] . '_country'); ?>" value="IR" />
					<input type="hidden" name="cyn_address_form_source" value="dashboard_modal" />
					<?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
				</div>
			</form>
		</div>
	</div>
<?php endforeach; ?>
<?php
do_action('woocommerce_account_dashboard');
do_action('woocommerce_before_my_account'); // deprecated
do_action('woocommerce_after_my_account');  // deprecated