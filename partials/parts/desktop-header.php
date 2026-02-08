<?php

/**
 * Desktop Header
 * @package CyanThemeSetup
 */

use Cyan\Theme\Helpers\Icon;

$header_light = apply_filters('cyan_header_light', (bool) ($GLOBALS['cyan_header_light'] ?? false));
$header_light_class = 'md:absolute md:!w-full md:[&_a]:text-white md:[&_li_ul_a]:duration-200 md:[&_li_ul_a]:transition-all md:[&_li_ul_a:hover]:text-cynYellow md:[&_span]:text-white md:[&_i]:text-white md:[&_li]:text-white md:[&_li:hover]:text-cynYellow md:[&_.sub-menu_a]:text-cynBlack md:[&_.sub-menu_li]:text-cynBlack md:[&_.header-dropdown_a]:text-cynBlack md:[&_.header-dropdown_span]:text-cynBlack';
?>

<section id="desktop-header" class="container z-50 <?php echo $header_light ? $header_light_class : ''; ?>">

	<div class="flex justify-between items-center max-lg:[&>div]:flex-1 py-4 lg:py-8">

		<div class="mobile-menu flex lg:hidden gap-2">
			<div class="p-2.5 rounded-full text-cynBlack border border-[#ECECEC] cursor-pointer" modal-opener data-modal-name="menu-modal">
				<i class="size-6 [&_svg]:scale-x-[-1] [&_svg]:transform [&_svg]:stroke-[1.5] text-cynBlack">
					<?php Icon::print('menu-burger-square-6') ?>
				</i>
			</div>

			<a href="<?= get_site_url() . '/?s=&search-type=all' ?>" class="lg:hidden block rounded-full text-cynBlack p-2.5 border border-[#ECECEC]">
				<i class="size-6 [&_svg]:stroke-[1.5]">
					<?php Icon::print('Search,-Loupe'); ?>
				</i>
			</a>
		</div>

		<div class="flex gap-11 items-center justify-center">

			<div class="logo hidden lg:flex">
				<?php the_custom_logo(); ?>
			</div>

			<a href="<?= get_site_url(); ?>" class="logo flex lg:hidden size-11">
				<img src="<?php echo get_option('logo_mobile_header'); ?>" alt="logo" class="w-full h-full object-contain">
			</a>

			<div class="desktop-menu hidden lg:flex">
				<?php wp_nav_menu([
					'menu_id' => 'main-menu',
					'menu_class' => 'gap-8 text-xs font-medium flex text-cynBlack [&>li:hover]:text-cynYellow [&>li>ul>li:hover]:text-cynYellow [&_li]:flex [&_li]:items-center [&_li]:duration-200 [&_li]:transition-all [&_li_a_svg]:transition-all [&_li_a_svg]:duration-300 [&_li:hover_svg]:rotate-180',
					'depth' => '3',
					'theme_location' => 'header-menu',
					'container' => 'ul'
				]); ?>
			</div>

		</div>

		<div class="flex justify-end items-center">

			<a href="<?= get_site_url() . '/?s=&search-type=all' ?>" class="hidden lg:flex size-10 items-center justify-center">
				<i class="size-6 [&_svg]:stroke-[1.5] text-cynBlack">
					<?php Icon::print('Search,-Loupe'); ?>
				</i>
			</a>

			<a href="<?= get_site_url() . '/my-wallet' ?>" class="hidden lg:flex size-10 items-center justify-center">
				<i class="size-6 [&_svg]:stroke-[1.5] text-cynBlack">
					<?php Icon::print('wallet'); ?>
				</i>
			</a>

			<a href="<?= get_site_url() . '/cart' ?>" class="hidden lg:flex size-10 items-center justify-center">
				<i class="size-6 [&_svg]:stroke-[1.5] text-cynBlack">
					<?php Icon::print('Shopping-Cart'); ?>
				</i>
			</a>

			<div class="relative flex justify-center group" id="login-btn">
				<a href="<?= !is_user_logged_in() ? get_site_url() . '/login' : '#' ?>" class="flex items-center justify-center gap-1 text-cynBlack bg-white py-2.5 md:py-1.5 ps-4 pe-3 rounded-2xl text-xs font-medium border border-cynYellow group-hover:border-cynYellow transition-all duration-300">

					<span class="whitespace-nowrap !text-cynBlack"><?= is_user_logged_in() ? esc_html(wp_get_current_user()->display_name) : __('ورود/ثبت نام', 'jonubgard'); ?></span>

					<?php if (is_user_logged_in()): ?>
						<i class="size-6 [&_svg]:stroke-[1.5] !text-cynBlack group-hover:text-cynYellow group-hover:rotate-180 transition-all duration-300">
							<?php Icon::print('Arrow-28'); ?>
						</i>
					<?php else: ?>
						<i class="size-6 [&_svg]:stroke-[1.5] !text-cynBlack">
							<?php Icon::print('login-enter-arrow-right-circle'); ?>
						</i>
					<?php endif; ?>

				</a>

				<?php if (is_user_logged_in()) : ?>
					<div class="header-dropdown absolute top-13 md:top-11 left-0 w-40 p-4 bg-cynWhite text-cynBlack rounded-3xl border border-cynYellow pointer-events-none group-hover:pointer-events-auto invisible group-hover:visible opacity-0 group-hover:opacity-100 transition-all duration-300">

						<?php wp_nav_menu([
							'menu_id' => 'login-menu',
							'menu_class' => 'flex flex-col gap-2 text-xs font-medium text-cynBlack [&_li]:first:border-t-0 [&_li]:border-t [&_li]:border-[#F4F4F6] [&_li]:py-2 [&>li>a:hover]:text-cynYellow [&_li_a]:duration-200 [&_li_a]:transition-all',
							'depth' => '3',
							'theme_location' => 'login-menu',
							'container' => 'ul'
						]); ?>

					</div>
				<?php endif; ?>

			</div>

		</div>

	</div>

</section>