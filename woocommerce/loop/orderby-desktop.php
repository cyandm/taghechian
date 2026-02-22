<?php

/**
 * Orderby form for desktop (xl) only: price low-to-high and high-to-low
 *
 * @package CyanTheme
 */

if (! defined('ABSPATH')) {
	exit;
}

$id_suffix = wp_unique_id();
$orderby   = isset($orderby) ? $orderby : (isset($_GET['orderby']) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : 'menu_order');
$options   = isset($catalog_orderby_options) && is_array($catalog_orderby_options) ? $catalog_orderby_options : [
	'price'      => __('مرتب‌سازی بر اساس ارزان‌ترین', 'taghechian'),
	'price-desc' => __('مرتب‌سازی بر اساس گران‌ترین', 'taghechian'),
];
if (! array_key_exists($orderby, $options)) {
	$orderby = key($options);
}
?>
<form class="woocommerce-ordering h-full w-full" method="get">
	<select
		name="orderby"
		class="orderby h-full w-full cursor-pointer"
		id="woocommerce-orderby-desktop-<?php echo esc_attr($id_suffix); ?>"
		aria-label="<?php esc_attr_e('مرتب‌سازی', 'taghechian'); ?>">
		<?php foreach ($options as $id => $name) : ?>
			<option value="<?php echo esc_attr($id); ?>" <?php selected($orderby, $id); ?>><?php echo esc_html($name); ?></option>
		<?php endforeach; ?>
	</select>
	<input type="hidden" name="paged" value="1" />
	<?php wc_query_string_form_fields(null, array('orderby', 'submit', 'paged', 'product-page')); ?>
</form>