<?php

/**
 * The Template for displaying product archives
 *
 * @package CyanTheme
 * @version 8.6.0
 */

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

get_header();
?>

<main class="archive-product">

	<!-- Breadcrumb Section -->
	<?php Templates::getPart('breadcrumb'); ?>

	<section class="grid grid-cols-1 lg:grid-cols-13 gap-6 container">

		<!-- sidebar -->
		<aside class="lg:col-span-4 xl:col-span-3 order-2 lg:order-1 max-lg:hidden">
			<?php wc_get_template('global/sidebar.php'); ?>
		</aside>

		<!-- main content -->
		<div class="lg:col-span-9 xl:col-span-10 order-1 lg:order-2">
			<?php
			/**
			 * Hook: woocommerce_before_main_content.
			 */
			do_action('woocommerce_before_main_content');
			// Show catalog ordering only in header, not again below the loop
			remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
			?>

			<?php
			$archive_filters_base_url = is_shop()
				? get_permalink(wc_get_page_id('shop'))
				: (is_product_taxonomy() ? get_term_link(get_queried_object()) : get_permalink(wc_get_page_id('shop')));
			$archive_filters_current = isset($_GET['orderby']) ? sanitize_text_field(wp_unslash($_GET['orderby'])) : '';
			$archive_filters_params  = isset($_GET) ? wp_unslash($_GET) : array();
			unset($archive_filters_params['paged']);

			$archive_orderby_filters = array(
				array(
					'orderby' => 'popularity',
					'label'   => __('محبوب ترین محصولات', 'taghechian'),
					'icon'    => 'Heart,-Favorite,-Love',
				),
				array(
					'orderby' => 'sale',
					'label'   => __('محصولات تخفیف دار', 'taghechian'),
					'icon'    => 'Sale,-Discount,-Promotion-6',
				),
				array(
					'orderby' => 'date',
					'label'   => __('جدیدترین محصولات', 'taghechian'),
					'icon'    => 'fire',
				),
			);
			?>
			<div class="flex justify-between items-center">
				<h1 class="text-3xl font-normal font-[Dinar]"><?php _e('فروشگاه', 'taghechian'); ?></h1>

				<div class="flex gap-2">
					<?php foreach ($archive_orderby_filters as $filter) : ?>
						<?php
						$params = $archive_filters_params;
						$active = ($archive_filters_current === $filter['orderby']);
						if ($active) {
							unset($params['orderby']);
						} else {
							$params['orderby'] = $filter['orderby'];
						}
						$filter_url = add_query_arg($params, $archive_filters_base_url);
						?>
						<a href="<?php echo esc_url($filter_url); ?>" class="max-xl:hidden py-3 px-3 rounded-3xl flex gap-1 items-center transition-colors no-underline <?php echo $active ? 'bg-cynYellow' : 'bg-cynBgItem/15 hover:bg-cynYellow'; ?>">
							<span class="text-sm font-medium text-cynBlack"><?php echo esc_html($filter['label']); ?></span>
							<i class="size-5 stroke-[1.5] text-cynBlack">
								<?php Icon::print($filter['icon']); ?>
							</i>
						</a>
					<?php endforeach; ?>

					<div class="lg:hidden cursor-pointer py-3 px-3 rounded-3xl flex gap-1 items-center transition-all duration-300 bg-cynBgItem/15 hover:bg-cynYellow" role="button" tabindex="0" aria-label="<?php esc_attr_e('نمایش فیلتر ها', 'taghechian'); ?>" modal-opener data-modal-name="filter-modal">
						<i class="size-5 text-cynBlack">
							<?php Icon::print('Filter,-Sort-1'); ?>
						</i>
						<span class="text-sm font-medium text-cynBlack"><?php esc_html_e('نمایش فیلتر ها', 'taghechian'); ?></span>
					</div>

					<?php if (wc_get_loop_prop('is_paginated') && woocommerce_products_will_display()) : ?>
						<?php
						$orderby_desktop_options = array(
							'price'      => __('مرتب‌سازی بر اساس ارزان‌ترین', 'taghechian'),
							'price-desc' => __('مرتب‌سازی بر اساس گران‌ترین', 'taghechian'),
						);
						?>

						<!-- Mobile: all sort options -->
						<div class="xl:hidden cursor-pointer relative py-3 px-3 rounded-3xl flex gap-1 items-center transition-all duration-300 bg-cynBgItem/15 hover:bg-cynYellow">
							<i class="size-5 text-cynBlack">
								<?php Icon::print('sort-ascending'); ?>
							</i>
							<span class="text-sm font-medium text-cynBlack"><?php esc_html_e('مرتب سازی', 'taghechian'); ?></span>
							<div class="absolute inset-0 opacity-0">
								<?php
								remove_filter('woocommerce_catalog_orderby', array(\Cyan\Theme\Classes\WooCommerce::class, 'catalogOrderbyRemoveOptions'), 20);
								woocommerce_catalog_ordering();
								add_filter('woocommerce_catalog_orderby', array(\Cyan\Theme\Classes\WooCommerce::class, 'catalogOrderbyRemoveOptions'), 20);
								?>
							</div>
						</div>

						<!-- Desktop xl: price low-to-high and high-to-low only -->
						<div class="hidden xl:flex cursor-pointer relative py-3 px-3 rounded-3xl gap-1 items-center transition-all duration-300 bg-cynBgItem/15 hover:bg-cynYellow">
							<i class="size-5 text-cynBlack">
								<?php Icon::print('sort-ascending'); ?>
							</i>
							<span class="text-sm font-medium text-cynBlack"><?php esc_html_e('مرتب سازی', 'taghechian'); ?></span>
							<div class="absolute inset-0 opacity-0">
								<?php wc_get_template('loop/orderby-desktop.php', array('catalog_orderby_options' => $orderby_desktop_options)); ?>
							</div>
						</div>
					<?php endif; ?>

				</div>

			</div>

			<?php
			if (woocommerce_product_loop()) :

				/**
				 * Hook: woocommerce_before_shop_loop.
				 */
				do_action('woocommerce_before_shop_loop');
			?>

				<hr class="border-cynBgItem/30 h-px w-full mb-2 mt-5 xl:hidden">

				<?php
				$archive_active_filters = \Cyan\Theme\Classes\WooCommerce::getArchiveActiveFilters($archive_filters_base_url);
				?>
				<?php if (! empty($archive_active_filters)) : ?>
					<div class="flex gap-2 xl:mt-5 flex-wrap items-center">
						<?php foreach ($archive_active_filters as $filter) : ?>
							<a href="<?php echo esc_url($filter['url']); ?>" class="flex items-center justify-center gap-1 px-3 py-1 bg-cynBgItem/15 rounded-xl group no-underline hover:bg-cynYellow/80 transition-all duration-300" title="<?php esc_attr_e('حذف فیلتر', 'taghechian'); ?>">
								<span class="p-0.5 bg-[#c2c2c2] group-hover:bg-white transition-all duration-300 rounded-full size-5 flex items-center justify-center shrink-0">
									<i class="size-5 stroke-[1.5] text-cynWhite group-hover:text-cynYellow transition-all duration-300 pointer-events-none">
										<?php Icon::print('Delete,-Disabled'); ?>
									</i>
								</span>
								<span class="text-xs font-medium text-cynBlack"><?php echo esc_html($filter['label']); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<hr class="border-cynBgItem/30 h-px w-full mt-2 mb-5 xl:hidden">

				<!-- Products Grid -->
				<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 [&_img]:h-[230px] lg:[&_img]:h-[260px] mt-5">
					<?php
					if (wc_get_loop_prop('total')) {
						while (have_posts()) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action('woocommerce_shop_loop');

							// Load custom product card template
							Templates::getCard('product');
						}
					}
					?>
				</div>

				<?php
				/**
				 * Hook: woocommerce_after_shop_loop.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action('woocommerce_after_shop_loop');
				?>

			<?php
			else :
				/**
				 * Hook: woocommerce_no_products_found.
				 */
				do_action('woocommerce_no_products_found');
			endif;

			/**
			 * Hook: woocommerce_after_main_content.
			 */
			do_action('woocommerce_after_main_content');
			?>
		</div>
	</section>

	<?php Templates::getPart('mobile-filter'); ?>
</main>

<?php
get_footer();
