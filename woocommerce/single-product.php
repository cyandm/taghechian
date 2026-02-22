<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
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
use Cyan\Theme\Helpers\Templates;

// Get current product
$product = wc_get_product(get_the_ID());

// Check if product exists
if (!$product) {
	return;
}

// Get product average rating
$average_rating = $product->get_average_rating(); // e.g. 4.2
$review_count = $product->get_review_count(); // Number of reviews

// Set default rating if no reviews
if ($average_rating == 0) {
	$average_rating = 5; // Or any default value you want
}

// Calculate full and half stars
$full_stars = floor($average_rating); // Full stars
$half_star = ($average_rating - $full_stars) * 100; // Half star percentage
$empty_stars = 5 - ceil($average_rating); // Empty stars

// Get featured image as fallback for video covers
$featured_image_id = get_post_thumbnail_id(get_the_ID());

$product_certificate = get_field('product_certificate', $product->get_id());
$product_details = get_field('product_details', $product->get_id());
$product_care = get_field('product_care', $product->get_id());

// Get related products with smart logic
$related_product_ids = [];
$current_title = get_the_title();
$product_categories = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'ids']);
$product_tags = wp_get_post_terms($product->get_id(), 'product_tag', ['fields' => 'ids']);

// Step 1: Find products with similar name
$similar_name_query = new WP_Query([
	'post_type' => 'product',
	'posts_per_page' => 12,
	'post__not_in' => [$product->get_id()],
	's' => $current_title,
	'fields' => 'ids'
]);

if ($similar_name_query->have_posts()) {
	$related_product_ids = $similar_name_query->posts;
}

// Step 2: If less than 4 or no similar name found, add products from same category
if (count($related_product_ids) < 4 && !empty($product_categories)) {
	$category_query = new WP_Query([
		'post_type' => 'product',
		'posts_per_page' => 12 - count($related_product_ids),
		'post__not_in' => array_merge([$product->get_id()], $related_product_ids),
		'tax_query' => [
			[
				'taxonomy' => 'product_cat',
				'field' => 'term_id',
				'terms' => $product_categories,
			]
		],
		'fields' => 'ids'
	]);

	if ($category_query->have_posts()) {
		$related_product_ids = array_merge($related_product_ids, $category_query->posts);
	}
}

// Step 3: If still not enough, add products with same tags
if (count($related_product_ids) < 12 && !empty($product_tags)) {
	$tag_query = new WP_Query([
		'post_type' => 'product',
		'posts_per_page' => 12 - count($related_product_ids),
		'post__not_in' => array_merge([$product->get_id()], $related_product_ids),
		'tax_query' => [
			[
				'taxonomy' => 'product_tag',
				'field' => 'term_id',
				'terms' => $product_tags,
			]
		],
		'fields' => 'ids'
	]);

	if ($tag_query->have_posts()) {
		$related_product_ids = array_merge($related_product_ids, $tag_query->posts);
	}
}

// Final query to get the actual product posts
$related_products_query = new WP_Query([
	'post_type' => 'product',
	'posts_per_page' => 12,
	'post__in' => $related_product_ids,
	'orderby' => 'post__in', // Maintain the priority order
]);

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="single-product-page container">

	<?php do_action('woocommerce_before_single_product'); ?>

	<section class="flex flex-col md:flex-row gap-3">

		<?php
		$featured_id  = $product->get_image_id();
		$gallery_ids = $product->get_gallery_image_ids();
		$image_ids   = array_filter(array_merge([$featured_id], $gallery_ids ?: []));
		if (empty($image_ids)) {
			$image_ids = [0];
		}
		?>

		<div class="w-full md:w-1/2 flex flex-col-reverse md:flex-row gap-2 h-fit">
			<!-- Thumbnails Slider -->
			<div class="flex-shrink-0 flex md:flex-col gap-2 order-first md:order-1">
				<swiper-container
					id="product-gallery-thumbs"
					class="product-gallery-thumbs w-full h-[135px] md:w-[100px] lg:w-[120px] md:h-[480px] 2xl:h-[564px]"
					slides-per-view="auto"
					space-between="8"
					direction="horizontal"
					breakpoints='{"768":{"direction":"vertical","slidesPerView":"auto","spaceBetween":8}}'>
					<?php foreach ($image_ids as $img_id) : ?>
						<?php if ($img_id) : ?>
							<swiper-slide class="w-[120px] h-[135px] md:w-[100px] lg:w-[120px] cursor-pointer opacity-60 hover:opacity-100 transition-opacity [&.swiper-slide-thumb-active]:!opacity-100 overflow-hidden">
								<div class="w-full h-full">
									<?php echo wp_get_attachment_image($img_id, 'thumbnail', false, ['class' => 'w-full h-full object-cover']); ?>
								</div>
							</swiper-slide>
						<?php else : ?>
							<swiper-slide class="!w-[120px] !h-[135px] cursor-pointer opacity-60 [&.swiper-slide-thumb-active]:!opacity-100">
								<div class="w-full h-full"><?php echo wc_placeholder_img('thumbnail'); ?></div>
							</swiper-slide>
						<?php endif; ?>
					<?php endforeach; ?>
				</swiper-container>
			</div>

			<!-- Main Gallery Slider -->
			<div class="relative flex-1 overflow-hidden order-2 md:order-2">
				<swiper-container
					id="product-gallery-main"
					class="product-gallery-main w-full h-[360px] sm:h-[480px] 2xl:h-[564px] overflow-hidden"
					slides-per-view="1"
					space-between="0"
					loop="true"
					thumbs-swiper="#product-gallery-thumbs"
					navigation="true"
					navigation-next-el="#productGalleryNext"
					navigation-prev-el="#productGalleryPrev">
					<?php foreach ($image_ids as $img_id) : ?>
						<?php if ($img_id) : ?>
							<swiper-slide class="!h-full">
								<div class="w-full h-full flex items-center justify-center bg-[#FCFCFC] overflow-hidden">
									<?php echo wp_get_attachment_image($img_id, 'full', false, ['class' => 'w-full h-full object-cover']); ?>
								</div>
							</swiper-slide>
						<?php else : ?>
							<swiper-slide class="!h-full">
								<div class="w-full h-full flex items-center justify-center bg-[#FCFCFC] overflow-hidden">
									<?php echo wc_placeholder_img('woocommerce_single'); ?>
								</div>
							</swiper-slide>
						<?php endif; ?>
					<?php endforeach; ?>
				</swiper-container>
				<!-- Navigation Buttons -->
				<div class="absolute left-1/2 -translate-x-1/2 bottom-3 z-10 flex items-center justify-center gap-3">
					<button type="button" id="productGalleryPrev" class="size-9 rounded-full bg-cynBlack text-white flex items-center justify-center hover:opacity-90 transition-opacity duration-300" aria-label="<?php echo esc_attr(__('قبلی', 'taghechian')); ?>">
						<i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
					</button>
					<button type="button" id="productGalleryNext" class="size-9 rounded-full bg-cynBlack text-white flex items-center justify-center hover:opacity-90 transition-opacity duration-300" aria-label="<?php echo esc_attr(__('بعدی', 'taghechian')); ?>">
						<i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
					</button>
				</div>
			</div>
		</div>

		<div class="w-full md:w-1/2">

			<div class="flex flex-col gap-1">
				<h1 class="text-3xl font-[Dinar] leading-14 text-cynBlack"><?php echo get_the_title(); ?></h1>

				<div class="flex items-center gap-2">
					<?php
					$categories = get_the_terms(get_the_ID(), 'product_cat');
					if ($categories && !is_wp_error($categories)) :
						foreach ($categories as $category) :
							// Skip uncategorized
							if ($category->slug === 'uncategorized' || $category->slug === 'بدون-دسته-بندی') {
								continue;
							}
					?>
							<a href="<?php echo get_term_link($category); ?>" class="text-cynBlack text-xs font-medium py-1 px-3 rounded-md bg-cynBgItem/15">
								<?php echo $category->name; ?>
							</a>
					<?php
						endforeach;
					endif;
					?>
				</div>
			</div>

			<?php
			$stock_quantity = $product->get_stock_quantity();
			$is_in_stock = $product->is_in_stock();
			?>

			<div id="stock-status-wrapper" class="mt-3.5 [transition:opacity_0.3s_ease,transform_0.3s_ease] <?php echo (!$is_in_stock || ($stock_quantity !== null && $stock_quantity <= 4)) ? '' : 'hidden'; ?>">
				<div class="py-1 px-3 rounded-md bg-[#dd4a4a]/14 flex items-center justify-center w-fit">
					<p class="text-[#dd4a4a] text-xs font-medium" id="stock-status-text">
						<?php if (!$is_in_stock) : ?>
							<?php _e('ناموجود', 'taghechian'); ?>
						<?php elseif ($stock_quantity !== null && $stock_quantity <= 4) : ?>
							<?php printf(__('فقط %s عدد باقیمانده', 'taghechian'), $stock_quantity); ?>
						<?php endif; ?>
					</p>
				</div>
			</div>

			<hr class="border-cynBgItem/30 h-px w-full my-5">

			<div class="flex items-center justify-between">
				<p class="text-cynBlack text-xl font-medium">
					<?php _e('قیمت', 'taghechian'); ?>
				</p>
				<p class="text-cynBlack text-xl font-medium transition-all duration-300" id="product-price-display">
					<?php
					// Check stock status
					if (!$is_in_stock) {
						// Out of stock - show "unavailable" label instead of price
						echo '<span class="text-[#dd4a4a]">' . __('ناموجود', 'taghechian') . '</span>';
					} elseif ($product->is_type('variable')) {
						$prices = $product->get_variation_prices(true);
						$min_price = current($prices['price']);
						$max_price = end($prices['price']);

						if ($min_price === $max_price) {
							// All variations have same price
							echo wc_price($min_price);
						} else {
							// Different prices - show minimum with "from" prefix
							echo sprintf(__('از %s', 'taghechian'), wc_price($min_price));
						}
					} else {
						// Simple product
						echo $product->get_price_html();
					}
					?>
				</p>
			</div>

			<hr class="border-cynBgItem/30 h-px w-full my-5">

			<div class="flex flex-col gap-3 w-full">

				<div class="flex items-center justify-between">

					<div class="flex items-center gap-2">

						<p class="text-cynBlack text-xl font-medium">
							<?php _e('انتخاب سایز', 'taghechian'); ?>
						</p>

						<div class="text-cynBlue flex items-center" id="sizeGuide">
							<span class="text-xs font-normal">
								<?php _e('راهنمای سایز', 'taghechian'); ?>
							</span>

							<i class="size-4"><?php Icon::print('ruler-1'); ?></i>
						</div>

					</div>

					<div class="w-fit xl:hidden">

						<div class="flex items-center gap-2">

							<button id="sizePrev" class="text-cynBlack cursor-pointer pointer-events-auto">
								<div class="size-5">
									<?php icon::print('Arrow-19') ?>
								</div>
							</button>

							<button id="sizeNext" class="text-cynBlack cursor-pointer pointer-events-auto">
								<div class="size-5">
									<?php icon::print('Arrow-27') ?>
								</div>
							</button>

						</div>

					</div>

				</div>

				<div class="w-full">
					<?php if ($product->is_type('variable')) : ?>
						<?php
						$available_variations = $product->get_available_variations();
						$attributes = $product->get_variation_attributes();

						// Get size attribute (try common attribute names)
						$size_attribute = null;
						$size_attribute_name = '';
						foreach ($attributes as $attribute_name => $options) {
							$attr_slug = strtolower($attribute_name);
							if (strpos($attr_slug, 'size') !== false || strpos($attr_slug, 'سایز') !== false || strpos($attr_slug, 'اندازه') !== false) {
								$size_attribute = $options;
								$size_attribute_name = $attribute_name;
								break;
							}
						}

						if ($size_attribute) :
						?>
							<swiper-container
								class="size-swiper bg-[#f9f9f9]"
								slides-per-view="auto"
								space-between="0"
								grab-cursor="true"
								navigation="true"
								navigation-next-el="#sizeNext"
								navigation-prev-el="#sizePrev">
								<?php foreach ($size_attribute as $size) : ?>
									<swiper-slide class="!w-auto">
										<button
											type="button"
											class="size-option bg-[#f9f9f9] min-w-[72px] h-9 px-4 py-2 text-sm font-medium text-cynBlack/50 border border-transparent hover:rounded-lg hover:border-cynBgItem transition-all duration-300 hover:text-cynBlack [&.active]:text-cynBlack [&.active]:border-cynBgItem [&.active]:bg-white [&.active]:rounded-lg"
											data-size="<?php echo esc_attr($size); ?>"
											data-attribute="<?php echo esc_attr($size_attribute_name); ?>">
											<?php echo esc_html($size); ?>
										</button>
									</swiper-slide>
								<?php endforeach; ?>
							</swiper-container>
						<?php endif; ?>
					<?php endif; ?>
				</div>

			</div>

			<div class="flex flex-col gap-3 w-full mt-7">

				<div class="flex items-center">

					<p class="text-cynBlack text-xl font-medium">
						<?php _e('انتخاب رنگ', 'taghechian'); ?>
					</p>

				</div>

				<div class="w-full">
					<?php if ($product->is_type('variable')) : ?>
						<?php
						$available_variations = $product->get_available_variations();
						$attributes = $product->get_variation_attributes();

						// Get color attribute
						$color_attribute = null;
						$color_attribute_name = '';
						foreach ($attributes as $attribute_name => $options) {
							$attr_slug = strtolower($attribute_name);
							if (strpos($attr_slug, 'color') !== false || strpos($attr_slug, 'رنگ') !== false) {
								$color_attribute = $options;
								$color_attribute_name = $attribute_name;
								break;
							}
						}

						if ($color_attribute) :
						?>
							<div class="flex items-center gap-3">
								<?php
								foreach ($color_attribute as $color_term_slug) :
									// Get taxonomy name - check if it already has 'pa_' prefix
									$taxonomy_name = (strpos($color_attribute_name, 'pa_') === 0) ? $color_attribute_name : 'pa_' . $color_attribute_name;

									// Get term by slug
									$term = get_term_by('slug', $color_term_slug, $taxonomy_name);
									if ($term) {
										$color_code = get_term_meta($term->term_id, 'term_color', true);
										$color_code = $color_code ? $color_code : '#cccccc';
								?>
										<button
											type="button"
											class="color-option group grid grid-cols-[auto_0fr] hover:grid-cols-[auto_1fr] [&.active]:grid-cols-[auto_1fr] items-center rounded-full border border-transparent hover:border-cynBgItem [&.active]:border-cynBgItem [&.active_.color-circle]:border-cynBgItem/25 [&.active_.color-circle]:hover:border-cynBgItem/25 p-1 transition-all duration-300"
											data-color="<?php echo esc_attr($color_term_slug); ?>"
											data-color-name="<?php echo esc_attr($term->name); ?>"
											data-attribute="<?php echo esc_attr($color_attribute_name); ?>"
											title="<?php echo esc_attr($term->name); ?>"
											aria-label="<?php echo esc_attr($term->name); ?>">

											<span class="color-circle size-7 rounded-full border border-transparent hover:border-cynBgItem/25 transition-all duration-300 <?php echo $term->name == 'سفید' && $color_term_slug == 'white' ? '!border-cynBgItem/25' : ''; ?>" style="background-color: <?php echo esc_attr($color_code); ?>"></span>

											<div class="overflow-hidden">
												<span class="color-label block text-sm font-medium text-cynBlack whitespace-nowrap px-3">
													<?php echo esc_html($term->name); ?>
												</span>
											</div>
										</button>
								<?php
									}
								endforeach;
								?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>

			</div>

			<hr class="border-cynBgItem/30 h-px w-full my-5">

			<?php if ($product->is_type('variable')) : ?>
				<form class="variations_form cart" method="post" enctype="multipart/form-data" data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo htmlspecialchars(wp_json_encode($product->get_available_variations())); ?>">

					<!-- Hidden variation selects -->
					<?php
					$attributes = $product->get_variation_attributes();
					foreach ($attributes as $attribute_name => $options) :
						$selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) ? wc_clean(stripslashes(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)]))) : $product->get_variation_default_attribute($attribute_name);
					?>
						<select class="hidden variation-select" name="attribute_<?php echo sanitize_title($attribute_name); ?>" data-attribute_name="attribute_<?php echo sanitize_title($attribute_name); ?>" data-show_option_none="yes">
							<option value=""><?php echo wc_attribute_label($attribute_name); ?></option>
							<?php
							if (!empty($options)) {
								foreach ($options as $option) {
									echo '<option value="' . esc_attr($option) . '" ' . selected($selected, $option, false) . '>' . esc_html(apply_filters('woocommerce_variation_option_name', $option)) . '</option>';
								}
							}
							?>
						</select>
					<?php endforeach; ?>

					<input type="hidden" name="variation_id" class="variation_id" value="0" />
					<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />

					<div class="flex items-center justify-between gap-3 flex-wrap">
						<div class="flex items-center gap-2">
							<button type="button" id="shareBtn" class="rounded-full border border-cynBorder flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 share-product p-3" aria-label="<?php echo esc_attr(__('اشتراک', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Share-1'); ?></i>
							</button>

							<a href="<?php echo esc_url(get_comments_link()); ?>#reviews" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('نظرات', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Messages,-Chat-18'); ?></i>
							</a>

							<button type="button" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Heart'); ?></i>
							</button>
						</div>

						<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button primary-btn flex items-center justify-center gap-2 !py-3 !px-6 text-base font-medium text-cynBlack">
							<span><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
							<i class="size-5"><?php Icon::print('Basket,-Shopping-Cart-6'); ?></i>
						</button>
					</div>
				</form>
			<?php else : ?>
				<form class="cart" method="post" enctype="multipart/form-data">
					<div class="flex items-center justify-between gap-3 flex-wrap">
						<div class="flex items-center gap-2">
							<button type="button" id="shareBtn" class="rounded-full border border-cynBorder flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 share-product p-3" aria-label="<?php echo esc_attr(__('اشتراک', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Share-1'); ?></i>
							</button>

							<a href="<?php echo esc_url(get_comments_link()); ?>#reviews" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('نظرات', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Messages,-Chat-18'); ?></i>
							</a>

							<button type="button" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>">
								<i class="size-6 stroke-[1.5]"><?php Icon::print('Heart'); ?></i>
							</button>
						</div>

						<button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button primary-btn flex items-center justify-center gap-2 !py-3 !px-6 text-base font-medium text-cynBlack <?php echo !$product->is_in_stock() ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''; ?>" <?php echo !$product->is_in_stock() ? 'disabled' : ''; ?>>
							<span><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
							<i class="size-5"><?php Icon::print('Basket,-Shopping-Cart-6'); ?></i>
						</button>
					</div>
				</form>
			<?php endif; ?>

			<div class="text-cynBlack text-base font-medium mt-5">
				<span>
					<?php
					$comments_count = get_comments_number();
					if ($comments_count == 0) {
						_e('دیدگاهی برای این محصول ثبت نشده است', 'taghechian');
					} else {
						printf(__('%s دیدگاه برای این محصول', 'taghechian'), $comments_count);
					}
					?>
				</span>
			</div>

			<?php if ($product_certificate || $product_details || $product_care) : ?>

				<div class="mt-7 flex flex-col gap-3">

					<?php if ($product_certificate) : ?>
						<!-- product certificate -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-description" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('شناسنامه محصول', 'taghechian'); ?>
								</span>
								<i class="accordion-icon rotate-45 size-6 stroke-2 text-cynBlack transition-transform duration-300 flex-shrink-0">
									<?php Icon::print('Delete,-Disabled'); ?>
								</i>
							</button>
							<div class="accordion-content grid transition-all duration-300 ease-in-out" data-accordion-content="accordion-description" style="grid-template-rows: 0fr;">
								<div class="overflow-hidden">
									<div class="text-sm font-light leading-8 text-cynBlack/80 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:px-5 [&_h2]:py-2 [&_h2]:bg-[#f0f0f0] [&_h2]:rounded-l-3xl [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:px-5 [&_h3]:py-2 [&_h3]:bg-[#f0f0f0] [&_h3]:rounded-l-3xl [&_h4]:text-xl [&_h4]:font-semibold [&_h4]:px-5 [&_h4]:py-2 [&_h4]:bg-[#f0f0f0] [&_h4]:rounded-l-3xl [&_p]:px-2 [&_p]:py-3">
										<?php echo $product_certificate; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($product_details) : ?>

						<hr class="border-cynBgItem/30 h-px w-full my-3">

						<!-- product details -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-specifications" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('جزئیات محصول', 'taghechian'); ?>
								</span>
								<i class="accordion-icon rotate-45 size-6 stroke-2 text-cynBlack transition-transform duration-300 flex-shrink-0">
									<?php Icon::print('Delete,-Disabled'); ?>
								</i>
							</button>
							<div class="accordion-content grid transition-all duration-300 ease-in-out" data-accordion-content="accordion-specifications" style="grid-template-rows: 0fr;">
								<div class="overflow-hidden">
									<div class="text-sm font-light leading-8 text-cynBlack/80 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:px-5 [&_h2]:py-2 [&_h2]:bg-[#f0f0f0] [&_h2]:rounded-l-3xl [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:px-5 [&_h3]:py-2 [&_h3]:bg-[#f0f0f0] [&_h3]:rounded-l-3xl [&_h4]:text-xl [&_h4]:font-semibold [&_h4]:px-5 [&_h4]:py-2 [&_h4]:bg-[#f0f0f0] [&_h4]:rounded-l-3xl [&_p]:px-2 [&_p]:py-3">
										<?php echo $product_details; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ($product_care) : ?>

						<hr class="border-cynBgItem/30 h-px w-full my-3">

						<!-- product care -->
						<div class="accordion-item bg-white overflow-hidden">
							<button class="accordion-button w-full flex items-center justify-between text-right cursor-pointer" data-accordion-target="accordion-care" aria-expanded="false">
								<span class="font-medium text-xl text-cynBlack flex-1 text-right">
									<?php _e('نکات نگهداری', 'taghechian'); ?>
								</span>
								<i class="accordion-icon rotate-45 size-6 stroke-2 text-cynBlack transition-transform duration-300 flex-shrink-0">
									<?php Icon::print('Delete,-Disabled'); ?>
								</i>
							</button>
							<div class="accordion-content grid transition-all duration-300 ease-in-out" data-accordion-content="accordion-care" style="grid-template-rows: 0fr;">
								<div class="overflow-hidden">
									<div class="text-xs font-light leading-8 text-cynBlack/80 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:px-5 [&_h2]:py-2 [&_h2]:bg-[#f0f0f0] [&_h2]:rounded-l-3xl [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:px-5 [&_h3]:py-2 [&_h3]:bg-[#f0f0f0] [&_h3]:rounded-l-3xl [&_h4]:text-xl [&_h4]:font-semibold [&_h4]:px-5 [&_h4]:py-2 [&_h4]:bg-[#f0f0f0] [&_h4]:rounded-l-3xl [&_p]:px-2 [&_p]:py-3">
										<?php echo $product_care; ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>

		</div>

	</section>

	<section class="mt-14 flex flex-col gap-3 md:gap-5">

		<div class="max-md:text-center">
			<p class="text-3xl md:text-[40px] text-cynBlack leading-11 font-[Dinar]"><?php _e('شاید بپسندید', 'taghechian'); ?></p>
		</div>

		<div class="relative">
			<swiper-container class="w-full" slides-per-view="1.25" space-between="12" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 3.15 }, "768":  { "slidesPerView": 3.15 }, "1024": { "slidesPerView": 4.25 }, "1280": { "slidesPerView": 5, "centeredSlides": false }}' loop="true" autoplay="true" pagination="false" navigation="true" navigation-next-el="#relatedProductsNext" navigation-prev-el="#relatedProductsPrev">
				<?php while ($related_products_query->have_posts()) : $related_products_query->the_post(); ?>
					<swiper-slide>
						<?php Templates::getCard('product'); ?>
					</swiper-slide>
				<?php endwhile; ?>
			</swiper-container>

			<div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">
				<button id="relatedProductsPrev" class="bg-cynBlack p-1 cursor-pointer rounded-full pointer-events-auto">
					<i class="text-white size-5 stroke-[1.5]">
						<?php icon::print('Arrow-19') ?>
					</i>
				</button>
				<button id="relatedProductsNext" class="bg-cynBlack p-1 cursor-pointer rounded-full pointer-events-auto">
					<i class="text-white size-5 stroke-[1.5]">
						<?php icon::print('Arrow-27') ?>
					</i>
				</button>
			</div>

		</div>

	</section>

</main>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Get all available variations
		const variationsData = <?php echo wp_json_encode($product->is_type('variable') ? $product->get_available_variations() : []); ?>;
		const priceDisplay = document.getElementById('product-price-display');
		const stockWrapper = document.getElementById('stock-status-wrapper');
		const stockText = document.getElementById('stock-status-text');
		const addToCartButton = document.querySelector('.single_add_to_cart_button');

		// Store selected attributes
		let selectedAttributes = {};

		// Function to find matching variation and update price with animation
		function updatePrice() {
			if (variationsData.length === 0 || !priceDisplay) return;

			// Find matching variation
			const matchingVariation = variationsData.find(variation => {
				return Object.keys(selectedAttributes).every(attr => {
					const varAttr = variation.attributes['attribute_' + attr];
					return !varAttr || varAttr === '' || varAttr === selectedAttributes[attr];
				});
			});

			if (matchingVariation && matchingVariation.display_price) {
				// Update variation_id for add to cart
				const variationIdInput = document.querySelector('input.variation_id');
				if (variationIdInput) {
					variationIdInput.value = matchingVariation.variation_id;
				}

				// Update stock status
				updateStockStatus(matchingVariation);

				// Fade out animation
				priceDisplay.style.opacity = '0';
				priceDisplay.style.transform = 'translateY(-10px)';

				setTimeout(() => {
					// Check if variation is in stock
					if (!matchingVariation.is_in_stock) {
						// Out of stock - show "unavailable" label
						priceDisplay.innerHTML = '<span class="text-[#dd4a4a] text-lg"><?php echo esc_js(__('ناموجود', 'taghechian')); ?></span>';
					} else {
						// In stock - show price
						const priceHtml = matchingVariation.price_html;
						priceDisplay.innerHTML = priceHtml;
					}

					// Fade in animation
					priceDisplay.style.opacity = '1';
					priceDisplay.style.transform = 'translateY(0)';
				}, 150);
			}
		}

		// Function to update stock status based on variation
		function updateStockStatus(variation) {
			if (!stockWrapper || !stockText) return;

			const isInStock = variation.is_in_stock;
			const stockQty = variation.max_qty || null;

			// Update add to cart button state
			if (addToCartButton) {
				if (!isInStock) {
					addToCartButton.disabled = true;
					addToCartButton.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
				} else {
					addToCartButton.disabled = false;
					addToCartButton.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
				}
			}

			// Fade out
			stockWrapper.style.opacity = '0';
			stockWrapper.style.transform = 'translateY(-10px)';

			setTimeout(() => {
				if (!isInStock) {
					// Out of stock
					stockText.textContent = '<?php echo esc_js(__('ناموجود', 'taghechian')); ?>';
					stockWrapper.classList.remove('hidden');
				} else if (stockQty !== null && stockQty <= 4 && stockQty > 0) {
					// Low stock
					stockText.textContent = '<?php echo esc_js(__('فقط', 'taghechian')); ?> ' + stockQty + ' <?php echo esc_js(__('عدد باقیمانده', 'taghechian')); ?>';
					stockWrapper.classList.remove('hidden');
				} else {
					// Enough stock - hide
					stockWrapper.classList.add('hidden');
				}

				// Fade in
				stockWrapper.style.opacity = '1';
				stockWrapper.style.transform = 'translateY(0)';
			}, 150);
		}

		// Handle Color Options
		const colorOptions = document.querySelectorAll('.color-option');

		if (colorOptions.length > 0) {
			// Set first color as active by default
			colorOptions[0].classList.add('active');
			if (colorOptions[0].dataset.attribute && colorOptions[0].dataset.color) {
				const attributeName = colorOptions[0].dataset.attribute;
				const selectedColor = colorOptions[0].dataset.color;
				selectedAttributes[attributeName] = selectedColor;

				// Update hidden select
				const selectElement = document.querySelector('select[name="attribute_' + attributeName + '"]');
				if (selectElement) {
					selectElement.value = selectedColor;
				}
			}

			colorOptions.forEach(function(button) {
				button.addEventListener('click', function() {
					// Remove active from all color buttons
					colorOptions.forEach(btn => btn.classList.remove('active'));

					// Add active to clicked
					this.classList.add('active');

					// Store selected color
					const selectedColor = this.dataset.color;
					const attributeName = this.dataset.attribute;
					selectedAttributes[attributeName] = selectedColor;

					// Update hidden select
					const selectElement = document.querySelector('select[name="attribute_' + attributeName + '"]');
					if (selectElement) {
						selectElement.value = selectedColor;
					}

					// Update price
					updatePrice();
				});
			});
		}

		// Handle Size Options
		const sizeOptions = document.querySelectorAll('.size-option');

		if (sizeOptions.length > 0) {
			// Set first size as active by default
			sizeOptions[0].classList.add('active');
			if (sizeOptions[0].dataset.attribute && sizeOptions[0].dataset.size) {
				const attributeName = sizeOptions[0].dataset.attribute;
				const selectedSize = sizeOptions[0].dataset.size;
				selectedAttributes[attributeName] = selectedSize;

				// Update hidden select
				const selectElement = document.querySelector('select[name="attribute_' + attributeName + '"]');
				if (selectElement) {
					selectElement.value = selectedSize;
				}
			}

			sizeOptions.forEach(function(button) {
				button.addEventListener('click', function() {
					// Remove active from all
					sizeOptions.forEach(btn => btn.classList.remove('active'));

					// Add active to clicked
					this.classList.add('active');

					// Store selected size
					const selectedSize = this.dataset.size;
					const attributeName = this.dataset.attribute;
					selectedAttributes[attributeName] = selectedSize;

					// Update hidden select
					const selectElement = document.querySelector('select[name="attribute_' + attributeName + '"]');
					if (selectElement) {
						selectElement.value = selectedSize;
					}

					// Update price
					updatePrice();
				});
			});
		}

		// Initial price update
		if (priceDisplay && variationsData.length > 0) {
			setTimeout(() => {
				updatePrice();

				// Initial stock check for first variation
				const matchingVariation = variationsData.find(variation => {
					return Object.keys(selectedAttributes).every(attr => {
						const varAttr = variation.attributes['attribute_' + attr];
						return !varAttr || varAttr === '' || varAttr === selectedAttributes[attr];
					});
				});

				if (matchingVariation && addToCartButton) {
					if (!matchingVariation.is_in_stock) {
						addToCartButton.disabled = true;
						addToCartButton.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
					}
				}
			}, 100);
		}
	});
</script>

<?php get_footer(); ?>