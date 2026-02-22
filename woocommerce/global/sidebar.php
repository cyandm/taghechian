<?php

/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/sidebar.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

use Cyan\Theme\Helpers\Icon;

$sidebar_suffix = isset($sidebar_suffix) ? sanitize_html_class($sidebar_suffix) : '';
$sid = function ($id) use ($sidebar_suffix) {
	return $id . ($sidebar_suffix ? '-' . $sidebar_suffix : '');
};

$uncategorized = get_term_by('slug', 'uncategorized', 'product_cat');
$exclude_cat_ids = ($uncategorized && ! is_wp_error($uncategorized)) ? [(int) $uncategorized->term_id] : [];

$product_cats = get_terms([
	'taxonomy'   => 'product_cat',
	'parent'     => 0,
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
	'exclude'    => $exclude_cat_ids,
]);
$current_cat_id = is_tax('product_cat') ? get_queried_object_id() : 0;

$product_genders = get_terms([
	'taxonomy'   => 'product_gender',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
]);
$current_gender_slug = isset($_GET['product_gender']) ? sanitize_title(wp_unslash($_GET['product_gender'])) : '';

$product_poets = get_terms([
	'taxonomy'   => 'product_poet',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
]);
$current_poet_slug = isset($_GET['product_poet']) ? sanitize_title(wp_unslash($_GET['product_poet'])) : '';

// Base URL for attribute filters (size, color) – keep current query, drop paged
$shop_base_link = (is_shop() || is_product_taxonomy()) ? remove_query_arg('paged', get_pagenum_link(1, false)) : '';

// Query keys to preserve when switching category (so filters don’t reset)
$sidebar_filter_query_keys = ['min_price', 'max_price', 'instock', 'filter_size', 'query_type_size', 'filter_color', 'query_type_color', 'product_gender', 'product_poet', 'product_collection', 'product_side'];

if (! function_exists('taghechian_term_link_with_filters')) {
	/**
	 * Append current sidebar filter query args to a term link (e.g. category) so filters stay in URL.
	 *
	 * @param string $term_link Term URL (e.g. from get_term_link()).
	 * @return string
	 */
	function taghechian_term_link_with_filters($term_link)
	{
		global $sidebar_filter_query_keys;
		if (! is_array($sidebar_filter_query_keys) || empty($_GET)) {
			return $term_link;
		}
		$params = array_intersect_key($_GET, array_flip($sidebar_filter_query_keys));
		$params = array_map(function ($v) {
			return is_string($v) ? wc_clean(wp_unslash($v)) : $v;
		}, $params);
		return add_query_arg($params, $term_link);
	}
}

if (! function_exists('taghechian_taxonomy_filter_term_link')) {
	/**
	 * Build URL that toggles a single taxonomy filter (product_gender, product_poet, etc.) in the current URL.
	 *
	 * @param string $base_link  Base URL (with current query).
	 * @param string $taxonomy   Taxonomy name (query var).
	 * @param object $term       Term object.
	 * @param bool   $is_active  Whether this term is currently selected.
	 * @return string
	 */
	function taghechian_taxonomy_filter_term_link($base_link, $taxonomy, $term, $is_active)
	{
		if (! $base_link) {
			return '#';
		}
		$link = remove_query_arg($taxonomy, $base_link);
		if (! $is_active) {
			$link = add_query_arg($taxonomy, $term->slug, $link);
		}
		return $link;
	}
}

if (! function_exists('taghechian_layered_nav_term_link')) {
	/**
	 * Build layered nav filter URL for one attribute term (toggle slug in current filter).
	 *
	 * @param string $base_link  Base URL (with other query args).
	 * @param string $taxonomy   Attribute taxonomy e.g. 'pa_size', 'pa_color'.
	 * @param object $term       Term object (slug used).
	 * @return string Filter URL.
	 */
	function taghechian_layered_nav_term_link($base_link, $taxonomy, $term)
	{
		if (! function_exists('wc_attribute_taxonomy_slug') || ! $base_link) {
			return '#';
		}
		$filter_name    = 'filter_' . wc_attribute_taxonomy_slug($taxonomy);
		$current_filter = isset($_GET[$filter_name]) ? array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET[$filter_name])))) : array();
		$option_is_set  = in_array($term->slug, $current_filter, true);
		if ($option_is_set) {
			$current_filter = array_diff($current_filter, array($term->slug));
		} else {
			$current_filter[] = $term->slug;
		}
		$current_filter = array_values(array_filter($current_filter));
		$link           = remove_query_arg(array($filter_name, 'query_type_' . wc_attribute_taxonomy_slug($taxonomy)), $base_link);
		if (! empty($current_filter)) {
			asort($current_filter);
			$link = add_query_arg($filter_name, implode(',', $current_filter), $link);
			$link = add_query_arg('query_type_' . wc_attribute_taxonomy_slug($taxonomy), 'or', $link);
			$link = str_replace('%2C', ',', $link);
		}
		return $link;
	}
}

$attr_size_tax  = 'pa_size';
$attr_color_tax = 'pa_color';
$size_terms     = (taxonomy_exists($attr_size_tax) && $shop_base_link) ? get_terms(array('taxonomy' => $attr_size_tax, 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC')) : array();
$color_terms    = (taxonomy_exists($attr_color_tax) && $shop_base_link) ? get_terms(array('taxonomy' => $attr_color_tax, 'hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC')) : array();


$product_collections = get_terms([
	'taxonomy'   => 'product_collection',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
]);
$current_collection_slug = isset($_GET['product_collection']) ? sanitize_title(wp_unslash($_GET['product_collection'])) : '';

$product_side = get_terms([
	'taxonomy'   => 'product_side',
	'hide_empty' => true,
	'orderby'    => 'name',
	'order'      => 'ASC',
]);
$current_side_slug = isset($_GET['product_side']) ? sanitize_title(wp_unslash($_GET['product_side'])) : '';

?>

<aside class="shop-sidebar">
	<?php
	if (is_active_sidebar('shop')) {
		dynamic_sidebar('shop');
	}
	?>

	<?php
	$instock_active = isset($_GET['instock']) && $_GET['instock'] === '1';
	?>
	<div class="flex justify-between items-center gap-2 rounded-3xl bg-cynBgItem/15 p-4">
		<span class="text-cynBlack text-xs font-medium"><?php _e('نمایش کالاهای موجود', 'taghechian'); ?></span>
		<button type="button" role="switch" aria-checked="<?php echo $instock_active ? 'true' : 'false'; ?>" data-instock-toggle class="instock-toggle-btn <?php echo $instock_active ? 'is-on ' : ''; ?>relative h-6 w-11 flex-shrink-0 rounded-full transition-colors duration-200 focus:outline-none" aria-label="<?php esc_attr_e('نمایش کالاهای موجود', 'taghechian'); ?>">
			<span class="absolute top-1 start-1 h-4 w-4 rounded-full bg-white shadow-sm transition-transform duration-200" data-toggle-knob></span>
		</button>
	</div>

	<div class="filter-cat rounded-3xl bg-cynBgItem/15 p-4 mt-4">
		<!-- Product categories (main) -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-cat-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="true" aria-controls="<?php echo esc_attr($sid('filter-cat-main')); ?>">
			<span class="text-sm font-medium"><?php _e('دسته‌بندی کالاها', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(180deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>
		<div id="<?php echo esc_attr($sid('filter-cat-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-cat-main')); ?>" style="grid-template-rows: 1fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if (!empty($product_cats) && !is_wp_error($product_cats)) {
						foreach ($product_cats as $parent) {
							$children = get_terms([
								'taxonomy'   => 'product_cat',
								'parent'     => $parent->term_id,
								'hide_empty' => true,
								'orderby'    => 'name',
								'order'      => 'ASC',
							]);
							$has_children = !empty($children) && !is_wp_error($children);
							$parent_link = taghechian_term_link_with_filters(get_term_link($parent));
							$is_parent_current = ($current_cat_id === (int) $parent->term_id);
							$child_ids = $has_children ? array_map(function ($c) {
								return (int) $c->term_id;
							}, $children) : [];
							$current_in_children = in_array($current_cat_id, $child_ids, true);
							$parent_open = $is_parent_current || $current_in_children;
							$parent_id = $sid('filter-cat-' . (int) $parent->term_id);
					?>
							<div class="filter-cat-parent bg-cynWhite rounded-xl py-2 px-3 transition-all duration-300 <?php echo $has_children ? '' : 'hover:bg-cynYellow'; ?>">
								<?php if ($has_children) : ?>
									<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none text-sm text-cynBlack rounded-xl bg-white/60 hover:bg-white/80 transition-colors py-1.5 px-2 text-start border-none" data-accordion-target="<?php echo esc_attr($parent_id); ?>" data-accordion-icon-rotate="180" aria-expanded="<?php echo $parent_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($parent_id); ?>">
										<span class="text-xs font-medium leading-6"><?php echo esc_html($parent->name); ?></span>
										<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(<?php echo $parent_open ? '180' : '0'; ?>deg);">
											<?php Icon::print('Arrow-28'); ?>
										</i>
									</button>
									<div id="<?php echo esc_attr($parent_id); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($parent_id); ?>" style="grid-template-rows: <?php echo $parent_open ? '1fr' : '0fr'; ?>;">
										<div class="min-h-0 overflow-hidden">
											<div class="mt-3 flex flex-col gap-1.5">
												<?php
												foreach ($children as $child) {
													$child_link = taghechian_term_link_with_filters(get_term_link($child));
													$is_active = ($current_cat_id === (int) $child->term_id);
												?>
													<a href="<?php echo esc_url($child_link); ?>" class="block p-1 rounded-md text-[10px] text-cynBlack transition-all duration-300 <?php echo $is_active ? 'bg-cynYellow' : 'bg-[#F5F5F5] hover:bg-cynYellow'; ?>">
														<?php echo esc_html($child->name); ?>
													</a>
												<?php } ?>
											</div>
										</div>
									</div>
								<?php else : ?>
									<a href="<?php echo esc_url($parent_link); ?>" class="block py-1.5 px-2 rounded-xl text-xs font-medium leading-6 text-cynBlack transition-colors <?php echo $is_parent_current ? 'bg-cynYellow' : ''; ?>">
										<?php echo esc_html($parent->name); ?>
									</a>
								<?php endif; ?>
							</div>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="filter-gender rounded-3xl bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by gender -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-gender-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-gender-main')); ?>">
			<span class="text-sm font-medium"><?php _e('مناسب', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>
		<div id="<?php echo esc_attr($sid('filter-gender-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-gender-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ($shop_base_link && ! empty($product_genders) && ! is_wp_error($product_genders)) {
						foreach ($product_genders as $gender) {
							$gender_link = taghechian_taxonomy_filter_term_link($shop_base_link, 'product_gender', $gender, $current_gender_slug === $gender->slug);
							$is_active = ($current_gender_slug === $gender->slug);
					?>
							<a href="<?php echo esc_url($gender_link); ?>" class="block rounded-xl py-2 px-3 text-xs font-medium leading-6 text-cynBlack transition-all duration-300 bg-cynWhite hover:bg-cynYellow <?php echo $is_active ? 'bg-cynYellow' : ''; ?>">
								<?php echo esc_html($gender->name); ?>
							</a>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="filter-price rounded-3xl bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by price -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-price-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-price-main')); ?>">
			<span class="text-sm font-medium"><?php _e('رنج قیمت', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>

		<div id="<?php echo esc_attr($sid('filter-price-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-price-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ((is_shop() || is_product_taxonomy()) && class_exists('WC_Widget_Price_Filter')) {
						the_widget(
							'WC_Widget_Price_Filter',
							array('title' => ''),
							array(
								'before_widget' => '',
								'after_widget'  => '',
								'before_title'  => '',
								'after_title'   => '',
							)
						);
					}
					?>
				</div>
			</div>
		</div>
	</div>


	<div class="filter-poet rounded-3xl bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by poet -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-poet-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-poet-main')); ?>">
			<span class="text-sm font-medium"><?php _e('بر اساس شاعر', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>

		<div id="<?php echo esc_attr($sid('filter-poet-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-poet-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ($shop_base_link && ! empty($product_poets) && ! is_wp_error($product_poets)) {
						foreach ($product_poets as $poet) {
							$poet_link = taghechian_taxonomy_filter_term_link($shop_base_link, 'product_poet', $poet, $current_poet_slug === $poet->slug);
							$is_active = ($current_poet_slug === $poet->slug);
					?>
							<a href="<?php echo esc_url($poet_link); ?>" class="block rounded-xl py-2 px-3 text-xs font-medium leading-6 text-cynBlack transition-all duration-300 bg-cynWhite hover:bg-cynYellow <?php echo $is_active ? 'bg-cynYellow' : ''; ?>">
								<?php echo esc_html($poet->name); ?>
							</a>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<?php
	if ($shop_base_link && taxonomy_exists($attr_size_tax) && ! empty($size_terms) && ! is_wp_error($size_terms)) :
		$filter_size_name = 'filter_' . wc_attribute_taxonomy_slug($attr_size_tax);
		$current_size     = isset($_GET[$filter_size_name]) ? array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET[$filter_size_name])))) : array();
	?>
		<div class="filter-attr filter-size rounded-3xl bg-cynBgItem/15 p-4 mt-4">
			<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-size-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-size-main')); ?>">
				<span class="text-sm font-medium"><?php _e('سایز', 'taghechian'); ?></span>
				<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
					<?php Icon::print('Arrow-28'); ?>
				</i>
			</button>
			<div id="<?php echo esc_attr($sid('filter-size-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-size-main')); ?>" style="grid-template-rows: 0fr;">
				<div class="min-h-0 overflow-hidden">
					<div class="pt-3 flex flex-wrap gap-2">
						<?php foreach ($size_terms as $term) :
							$link       = taghechian_layered_nav_term_link($shop_base_link, $attr_size_tax, $term);
							$is_checked = in_array($term->slug, $current_size, true);
						?>
							<a href="<?php echo esc_url($link); ?>" class="inline-flex items-center justify-center min-h-10 min-w-14 rounded-xl py-2 px-3 text-sm font-normal text-cynBlack bg-cynWhite hover:bg-cynYellow transition-all duration-300 <?php echo $is_checked ? 'bg-cynYellow' : ''; ?>">
								<?php echo esc_html($term->name); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php
	if ($shop_base_link && taxonomy_exists($attr_color_tax) && ! empty($color_terms) && ! is_wp_error($color_terms)) :
		$filter_color_name = 'filter_' . wc_attribute_taxonomy_slug($attr_color_tax);
		$current_color     = isset($_GET[$filter_color_name]) ? array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET[$filter_color_name])))) : array();
	?>
		<div class="filter-attr filter-color rounded-3xl bg-cynBgItem/15 p-4 mt-4">
			<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-color-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-color-main')); ?>">
				<span class="text-sm font-medium"><?php _e('رنگ', 'taghechian'); ?></span>
				<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
					<?php Icon::print('Arrow-28'); ?>
				</i>
			</button>
			<div id="<?php echo esc_attr($sid('filter-color-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-color-main')); ?>" style="grid-template-rows: 0fr;">
				<div class="min-h-0 overflow-hidden">
					<div class="pt-3 flex flex-wrap items-center gap-1.5">
						<?php
						foreach ($color_terms as $term) :
							$link       = taghechian_layered_nav_term_link($shop_base_link, $attr_color_tax, $term);
							$is_checked = in_array($term->slug, $current_color, true);
							$color_code = get_term_meta($term->term_id, 'term_color', true) ?: get_term_meta($term->term_id, 'product_attribute_color', true) ?: get_term_meta($term->term_id, 'color', true);
							$color_code = $color_code ? $color_code : '#cccccc';
							$is_white   = ($term->name === 'سفید' && $term->slug === 'white');
						?>
							<a href="<?php echo esc_url($link); ?>" class="color-option group grid grid-cols-[auto_0fr] hover:grid-cols-[auto_1fr] [&.active]:grid-cols-[auto_1fr] items-center rounded-full border border-transparent hover:border-cynBgItem [&.active]:border-cynBgItem [&.active_.color-circle]:border-cynBgItem/25 [&.active_.color-circle]:hover:border-cynBgItem/25 p-1 transition-all duration-300 no-underline shrink-0 <?php echo $is_checked ? 'active' : ''; ?>" title="<?php echo esc_attr($term->name); ?>" aria-label="<?php echo esc_attr($term->name); ?>">
								<span class="color-circle size-7 rounded-full border border-transparent hover:border-cynBgItem/25 transition-all duration-300 <?php echo $is_white ? '!border-cynBgItem/25' : ''; ?>" style="background-color: <?php echo esc_attr($color_code); ?>"></span>
								<div class="overflow-hidden">
									<span class="color-label block text-sm font-medium text-cynBlack whitespace-nowrap px-3"><?php echo esc_html($term->name); ?></span>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="filter-collection rounded-3xl bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by collection -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-collection-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-collection-main')); ?>">
			<span class="text-sm font-medium"><?php _e('کالکشن', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>

		<div id="<?php echo esc_attr($sid('filter-collection-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-collection-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ($shop_base_link && ! empty($product_collections) && ! is_wp_error($product_collections)) {
						foreach ($product_collections as $collection) {
							$collection_link = taghechian_taxonomy_filter_term_link($shop_base_link, 'product_collection', $collection, $current_collection_slug === $collection->slug);
							$is_active = ($current_collection_slug === $collection->slug);
					?>
							<a href="<?php echo esc_url($collection_link); ?>" class="block rounded-xl py-2 px-3 text-xs font-medium leading-6 text-cynBlack transition-all duration-300 bg-cynWhite hover:bg-cynYellow <?php echo $is_active ? 'bg-cynYellow' : ''; ?>">
								<?php echo esc_html($collection->name); ?>
							</a>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="filter-side rounded-3xl bg-cynBgItem/15 p-4 mt-4">
		<!-- Product filter by side -->
		<button type="button" class="accordion-button flex w-full items-center justify-between gap-2 cursor-pointer list-none font-medium text-cynBlack py-1 text-start bg-transparent border-none" data-accordion-target="<?php echo esc_attr($sid('filter-side-main')); ?>" data-accordion-icon-rotate="180" aria-expanded="false" aria-controls="<?php echo esc_attr($sid('filter-side-main')); ?>">
			<span class="text-sm font-medium"><?php _e('یک رو و دو رو', 'taghechian'); ?></span>
			<i class="accordion-icon size-6 stroke-[1.5] transition-transform duration-300 flex-shrink-0" style="transform: rotate(0deg);">
				<?php Icon::print('Arrow-28'); ?>
			</i>
		</button>

		<div id="<?php echo esc_attr($sid('filter-side-main')); ?>" class="grid transition-[grid-template-rows] duration-300 ease-out !mt-0" data-accordion-content="<?php echo esc_attr($sid('filter-side-main')); ?>" style="grid-template-rows: 0fr;">
			<div class="min-h-0 overflow-hidden">
				<div class="pt-3 space-y-1.5">
					<?php
					if ($shop_base_link && ! empty($product_side) && ! is_wp_error($product_side)) {
						foreach ($product_side as $side) {
							$side_link = taghechian_taxonomy_filter_term_link($shop_base_link, 'product_side', $side, $current_side_slug === $side->slug);
							$is_active = ($current_side_slug === $side->slug);
					?>
							<a href="<?php echo esc_url($side_link); ?>" class="block rounded-xl py-2 px-3 text-xs font-medium leading-6 text-cynBlack transition-all duration-300 bg-cynWhite hover:bg-cynYellow <?php echo $is_active ? 'bg-cynYellow' : ''; ?>">
								<?php echo esc_html($side->name); ?>
							</a>
					<?php
						}
					}
					?>
				</div>
			</div>
		</div>
	</div>

</aside>
<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
