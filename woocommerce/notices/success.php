<?php

/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! $notices) {
	return;
}

?>

<?php foreach ($notices as $notice) : ?>
	<div class="woocommerce-message !border-r-2 !border-[#4f9658] !bg-[#8ce6922b] !backdrop:blur-2xl before:!content-['\e015'] before:!text-[#4f9658] before:!top-1/2 before:!translate-x-0 before:!-translate-y-1/2 after:!hidden" <?php echo wc_get_notice_data_attr($notice); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
																																																									?> role=" alert">
		<?php echo wc_kses_notice($notice['notice']); ?>
	</div>
<?php endforeach; ?>