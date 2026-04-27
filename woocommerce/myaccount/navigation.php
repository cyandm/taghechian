<?php

/**
 * My Account navigation - Custom Sidebar
 *
 * This template overrides the default WooCommerce navigation with a styled sidebar
 * that includes custom menu items (wallet, notifications, wishlist, points, support).
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if (! defined('ABSPATH')) {
	exit;
}

global $wp;
$current_endpoint = '';

$endpoint_aliases = array(
	'orders' => 'orders',
	'view-order' => 'orders',
	'downloads' => 'orders',
);

$allowed_endpoints = array('orders', 'view-order', 'downloads', 'wallet', 'notifications', 'wishlist', 'points', 'support');
if (! empty($wp->query_vars)) {
	foreach ($allowed_endpoints as $endpoint) {
		if (isset($wp->query_vars[$endpoint])) {
			$current_endpoint = isset($endpoint_aliases[$endpoint]) ? $endpoint_aliases[$endpoint] : $endpoint;
			break;
		}
	}
}

$menu_items = array(
	'' => array(
		'label' => 'اطلاعات حساب',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>',
	),
	'orders' => array(
		'label' => 'سفارش‌های من',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>',
	),
	'wallet' => array(
		'label' => 'کیف پول',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>',
	),
	'notifications' => array(
		'label' => 'اعلان ها',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>',
	),
	'wishlist' => array(
		'label' => 'علاقه‌مندی‌ها',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>',
	),
	'points' => array(
		'label' => 'امتیازات',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>',
	),
	'support' => array(
		'label' => 'پشتیبانی',
		'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
	),
);

$disabled_items = array('wallet', 'notifications', 'points');
$current_label = isset($menu_items[$current_endpoint]) ? $menu_items[$current_endpoint]['label'] : $menu_items['']['label'];
$current_icon = isset($menu_items[$current_endpoint]) ? $menu_items[$current_endpoint]['icon'] : $menu_items['']['icon'];
$logout_url = wp_logout_url(home_url('/'));
?>

<style>
	@keyframes myaccount-mobile-nav-open {
		from {
			opacity: 0;
			transform: translateY(-4px) scaleY(0.98);
		}

		to {
			opacity: 1;
			transform: translateY(0) scaleY(1);
		}
	}

	.myaccount-mobile-nav-panel {
		transform-origin: top;
		animation: myaccount-mobile-nav-open 180ms ease-out;
	}
</style>

<div class="lg:hidden">
	<details class="group relative">
		<summary class="list-none cursor-pointer">
			<div
				class="w-full px-6 py-2.5 rounded-2xl border border-[#FFD000] bg-white text-black flex flex-row-reverse items-center justify-between text-sm">
				<svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-open:rotate-180" fill="none"
					stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
				</svg>
				<span class="flex items-center gap-2 shrink-0">
					<span class="text-gray-700 shrink-0">
						<?php echo $current_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
					</span>
					<span><?php echo esc_html($current_label); ?></span>
				</span>
			</div>
		</summary>

		<div class="myaccount-mobile-nav-panel mt-2 bg-white border border-gray-200 rounded-2xl p-2 space-y-1">
			<?php foreach ($menu_items as $endpoint => $item): ?>
				<?php
				$is_disabled = in_array($endpoint, $disabled_items, true);
				$is_active = ($current_endpoint === $endpoint);
				$item_url = wc_get_account_endpoint_url($endpoint);
				$mobile_base_classes = 'w-full flex items-center justify-between px-4 py-2.5 text-sm rounded-full transition-all duration-200';
				$mobile_state_classes = $is_disabled
					? 'bg-gray-100 text-gray-400 cursor-not-allowed opacity-60'
					: ($is_active ? 'bg-yellow-400 text-gray-900' : 'text-gray-900 hover:bg-slate-100');
				$mobile_item_classes = $mobile_base_classes . ' ' . $mobile_state_classes;
				?>
				<?php if ($is_disabled): ?>
					<span class="<?php echo esc_attr($mobile_item_classes); ?>">
						<span class="flex items-center gap-2">
							<span class="text-gray-400"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
														?></span>
							<span><?php echo esc_html($item['label']); ?></span>
							<span class="text-[10px] bg-yellow-400 text-gray-900 px-2 py-0.5 rounded-full">بزودی</span>
						</span>
					</span>
				<?php else: ?>
					<a href="<?php echo esc_url($item_url); ?>" class="<?php echo esc_attr($mobile_item_classes); ?>">
						<span class="flex items-center gap-2">
							<span class="<?php echo esc_attr($is_active ? 'text-gray-900' : 'text-yellow-500'); ?>">
								<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
								?>
							</span>
							<span><?php echo esc_html($item['label']); ?></span>
						</span>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>

			<div class="border-t border-gray-200 my-2"></div>

			<button type="button" modal-opener data-modal-name="logout-confirm-modal"
				class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-gray-900 rounded-full transition-all duration-200 hover:bg-slate-100">
				<span class="flex items-center gap-2">
					<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
					</svg>
					<span class="font-[dinar]">خروج</span>
				</span>
			</button>
		</div>
	</details>
</div>

<div class="hidden lg:block sticky top-6 self-start bg-white rounded-2xl border-gray-200 border-1 p-4 space-y-2">
	<?php foreach ($menu_items as $endpoint => $item): ?>
		<?php
		$is_disabled = in_array($endpoint, $disabled_items, true);
		$url = wc_get_account_endpoint_url($endpoint);
		$is_active = ($current_endpoint === $endpoint);
		$base_classes = 'w-full flex items-center justify-between px-4 py-3 font-medium transition-all duration-300 rounded-full group';
		$enabled_classes = 'text-gray-900 hover:bg-slate-100';
		$active_classes = $is_active ? 'bg-yellow-400 text-gray-900' : '';
		$disabled_classes = 'opacity-60 cursor-not-allowed bg-gray-100 text-gray-400';
		$item_class = $base_classes . ' ' . ($is_disabled ? $disabled_classes : ($is_active ? $active_classes : $enabled_classes));
		?>
		<?php if ($is_disabled): ?>
			<span class="<?php echo esc_attr($item_class); ?>">
				<div class="flex items-center gap-2">
					<div class="text-gray-400">
						<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
					</div>
					<span><?php echo esc_html($item['label']); ?></span>
					<span class="text-xs bg-yellow-400 text-gray-900 px-2 py-0.5 rounded-full">بزودی</span>
				</div>
				<div class="w-5"></div>
			</span>
		<?php else: ?>
			<a href="<?php echo esc_url($url); ?>" class="<?php echo esc_attr($item_class); ?>">
				<div class="flex items-center gap-2">
					<div class="<?php echo esc_attr($is_active ? 'text-gray-900' : 'text-yellow-500'); ?>">
						<?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
					</div>
					<span><?php echo esc_html($item['label']); ?></span>
				</div>

				<div class="flex items-center gap-2">
					<?php if ($is_active): ?>
						<svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
						</svg>
					<?php else: ?>
						<span class="w-5"></span>
					<?php endif; ?>
				</div>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>

	<div class="border-t border-gray-200 my-4"></div>

	<button type="button" modal-opener data-modal-name="logout-confirm-modal"
		class="w-full flex items-center justify-between px-4 py-3 text-gray-900 font-medium transition-all duration-300 rounded-full group hover:bg-slate-100">
		<div class="flex items-center gap-2">
			<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
			</svg>
			<span class="font-[dinar]">خروج</span>
		</div>
	</button>
</div>

<div class="container flex justify-center items-center h-fit top-1/2 -translate-y-1/2 fixed inset-0 z-50 opacity-0 pointer-events-none w-full md:!w-4/10 data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500"
	modal data-modal-name="logout-confirm-modal" data-active="false">
	<div
		class="w-full px-6 py-5 bg-cynWhite rounded-2xl border border-cynBlack/10 shadow-item flex flex-col items-center gap-5">
		<div class="text-center space-y-2">
			<p class="text-[30px] font-[dinar] text-cynBlack leading-none">خروج از حساب</p>
			<p class="text-base text-cynBlack/70">ایا میخواهید از حساب کاربری خود خارج شوید؟</p>
		</div>

		<div class="w-full flex items-center justify-center gap-3">
			<a href="<?php echo esc_url($logout_url); ?>"
				class="w-[120px] h-10 rounded-full bg-cynYellow border border-cynYellow text-cynBlack text-sm font-medium flex items-center justify-center hover:bg-[#eac100] transition-colors duration-200">
				تایید
			</a>
			<button type="button" modal-closer data-modal-name="logout-confirm-modal"
				class="w-[120px] h-10 rounded-full border border-cynYellow text-cynBlack text-sm font-medium flex items-center justify-center hover:bg-yellow-50 transition-colors duration-200">
				انصراف
			</button>
		</div>
	</div>
</div>