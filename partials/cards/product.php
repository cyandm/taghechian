<?php
$args       = get_query_var('args', []);
$product_id = !empty($args['product']) ? (int) $args['product'] : get_the_ID();
$product    = wc_get_product($product_id);
if (!$product) {
    return;
}

$show_wishlist_remove = ! empty($args['show_wishlist_remove']);
$wishlist_remove_url  = ! empty($args['wishlist_remove_url']) ? esc_url($args['wishlist_remove_url']) : '';
?>

<div class="product-card relative">
    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="block">
        <div>
            <?php echo get_the_post_thumbnail($product_id, 'full', ['class' => 'object-cover w-full h-[360px]']); ?>
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-3 md:p-4 text-white flex justify-between items-end gap-1 md:gap-3 h-full bg-linear-cart">
            <p class="text-base font-medium"><?php echo esc_html(get_the_title($product_id)); ?></p>
            <p class="text-base font-medium"><?php echo esc_html(number_format((float) $product->get_price(), 0)); ?></p>
        </div>
    </a>

    <?php if ($show_wishlist_remove && $wishlist_remove_url !== '') : ?>
        <a href="<?php echo $wishlist_remove_url; ?>"
            class="absolute top-2 left-2 size-7 rounded-full bg-white/90 hover:bg-white text-[#cf255d] flex items-center justify-center transition-colors duration-200 z-10"
            aria-label="<?php esc_attr_e('حذف از علاقه‌مندی‌ها', 'taghechian'); ?>">
            <svg class="size-4" viewBox="0 0 20 20" aria-hidden="true">
                <path fill="currentColor"
                    d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656z" />
            </svg>
        </a>
    <?php endif; ?>
</div>