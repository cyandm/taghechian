<?php
$product_id = get_the_ID();
$product    = wc_get_product($product_id);
if (!$product) {
	return;
}
?>
<a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="search-card-product p-4 flex rounded-2xl border border-cynBlack/10 bg-white overflow-hidden text-cynBlack hover:border-cynYellow transition-colors">
	<div class="w-28 h-36 shrink-0">
		<?php echo get_the_post_thumbnail($product_id, 'thumbnail', ['class' => 'object-cover w-full h-full rounded-xl']); ?>
	</div>
	<div class="flex-1 min-w-0 p-1.5 flex flex-col justify-around">
		<h3 class="text-xl font-medium text-cynBlue line-clamp-2"><?php echo esc_html(get_the_title($product_id)); ?></h3>
		<p class="text-base font-medium text-cynBlue mt-3"><?php echo esc_html(number_format((float) $product->get_price(), 0)); ?></p>
		<p class="text-xs text-cynBlack/60 mt-2"><?php _e('محصولات', 'taghechian'); ?></p>
	</div>
</a>