<?php

/**
 * My Account Wishlist endpoint content.
 *
 * @package WooCommerce\Templates
 */

defined('ABSPATH') || exit;

use Cyan\Theme\Classes\WooCommerce as ThemeWooCommerce;
use Cyan\Theme\Helpers\Templates;

$wishlist_product_ids = ThemeWooCommerce::getCurrentUserWishlistProductIds();

if (! empty($wishlist_product_ids)) {
    $wishlist_query = new WP_Query(
        [
            'post_type'      => 'product',
            'post__in'       => array_map('absint', $wishlist_product_ids),
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]
    );
}
?>

<h2 class="text-2xl md:text-4xl font-[dinar] mb-3 text-gray-900">علاقه مندی ها</h2>
<section class="wishlist-account-page bg-white rounded-2xl p-4 md:p-6 lg:p-8">

    <?php if (! empty($wishlist_query) && $wishlist_query->have_posts()) : ?>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 wishlist-account-grid">
            <?php while ($wishlist_query->have_posts()) : ?>
                <?php
                $wishlist_query->the_post();
                $wishlist_product_id = get_the_ID();
                Templates::getCard(
                    'product',
                    [
                        'product'              => $wishlist_product_id,
                        'show_wishlist_remove' => true,
                        'wishlist_remove_url'  => ThemeWooCommerce::getWishlistRemoveUrl($wishlist_product_id),
                    ]
                );
                ?>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <div class="min-h-[220px] flex flex-col items-center justify-center gap-3 text-center">
            <p class="text-gray-500">هنوز محصولی به علاقه‌مندی‌ها اضافه نشده است.</p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-yellow-400 text-gray-900 hover:bg-yellow-500 transition-colors duration-200">
                مشاهده محصولات
            </a>
        </div>
    <?php endif; ?>
</section>