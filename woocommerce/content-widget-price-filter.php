<?php
/**
 * The template for displaying product price filter widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-price-filter.php
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

?>
<?php do_action( 'woocommerce_widget_price_filter_start', $args ); ?>

<form method="get" action="<?php echo esc_url( $form_action ); ?>" class="price-filter-form">
	<div class="price_slider_wrapper">
		<div class="price_slider" style="display:none;"></div>
		<div class="price_slider_amount" data-step="<?php echo esc_attr( $step ); ?>">
			<div class="flex flex-wrap items-center gap-2 mb-3">
				<label for="min_price" class="text-xs font-medium text-cynBlack shrink-0"><?php esc_html_e( 'شروع از', 'taghechian' ); ?></label>
				<input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( $current_min_price ); ?>" data-min="<?php echo esc_attr( $min_price ); ?>" placeholder="<?php echo esc_attr( $min_price ); ?>" class="price-input flex-1 min-w-0 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-cynBlack focus:outline-none focus:ring-2 focus:ring-cynYellow" />
				<span class="text-xs font-medium text-cynBlack shrink-0"><?php esc_html_e( 'تا', 'taghechian' ); ?></span>
				<input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( $current_max_price ); ?>" data-max="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php echo esc_attr( $max_price ); ?>" class="price-input flex-1 min-w-0 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-cynBlack focus:outline-none focus:ring-2 focus:ring-cynYellow" />
			</div>
			<div class="price_label mt-2 text-sm font-medium text-cynBlack" role="status" aria-live="polite">
				<span class="from"><?php echo wc_price( $current_min_price ); ?></span>
				<span class="mx-1">—</span>
				<span class="to"><?php echo wc_price( $current_max_price ); ?></span>
			</div>
			<?php echo wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ); ?>
		</div>
	</div>
</form>
<?php
$price_filter_script = "jQuery(document.body).on('price_slider_create', function() { jQuery('#min_price, #max_price').show(); });"
	. " jQuery(document.body).on('price_slider_change', function(e, min, max) { jQuery('.shop-sidebar .price-filter-form').submit(); });";
wp_add_inline_script( 'wc-price-slider', $price_filter_script, 'after' );
?>

<?php do_action( 'woocommerce_widget_price_filter_end', $args ); ?>
