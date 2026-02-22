<?php
$args       = get_query_var('args', []);
$product_id = !empty($args['product']) ? (int) $args['product'] : get_the_ID();
$product    = wc_get_product($product_id);
if (!$product) {
    return;
}
?>

<a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="product-card relative">
    <div class="">
        <?php echo get_the_post_thumbnail($product_id, 'full', ['class' => 'object-cover w-full h-[360px]']); ?>
    </div>
    <div class="absolute bottom-0 left-0 right-0 p-3 md:p-4 text-white flex justify-between items-end gap-1 md:gap-3 h-full bg-linear-cart">
        <p class="text-base font-medium"><?php echo esc_html(get_the_title($product_id)); ?></p>
        <p class="text-base font-medium"><?php echo esc_html(number_format((float) $product->get_price(), 0)); ?></p>
    </div>
</a>