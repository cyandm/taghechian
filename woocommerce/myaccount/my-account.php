<?php

/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/myaccount.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

wc_print_notices();

do_action('woocommerce_before_account_navigation');
?>

<div class="min-h-screen   md:p-8" dir="rtl">
	<div class="max-w-7xl mx-auto">
		<h1 class="text-3xl hidden lg:block font-[dinar] text-gray-900 mb-8">حساب من</h1>

		<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
			<!-- Sidebar Column (Navigation) -->
			<div class="lg:col-span-3 order-1 lg:order-1">
				<?php wc_get_template('myaccount/navigation.php'); ?>
			</div>

			<!-- Main Content Column -->
			<div class="lg:col-span-9 order-2 lg:order-2   ">
				<?php do_action('woocommerce_account_content'); ?>
			</div>
		</div>
	</div>
</div>

<?php do_action('woocommerce_after_account_navigation'); ?>