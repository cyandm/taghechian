<?php

use Cyan\Theme\Helpers\Icon;

$address_link = get_option('address_link');
$telegram_link = get_option('telegram_link');
$telegram_text = get_option('telegram_text');
$instagram_link = get_option('instagram_link');
$instagram_text = get_option('instagram_text');
$whatsapp_number = get_option('whatsapp_number');
$twitter_link = get_option('twitter_link');
$twitter_text = get_option('twitter_text');
$pinterest_link = get_option('pinterest_link');
$pinterest_text = get_option('pinterest_text');
$logo_mobile_menu = get_option('logo_mobile_menu');
?>

<section class="mobile-menu-scroll h-screen bg-white p-5 w-0 fixed inset-0 z-50 opacity-0 pointer-events-none overflow-y-auto data-[active='true']:w-full data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500" modal data-modal-name="menu-modal" data-active="false">

	<div class="flex justify-between items-center">

		<div class="size-8 flex justify-center items-center text-cynBlack" modal-closer data-modal-name="menu-modal">
			<?php Icon::print('Arrow,-Forward'); ?>
		</div>

		<?php if ($logo_mobile_menu) : ?>
			<div><img src="<?php echo $logo_mobile_menu ?>" alt="logo" class="w-full h-full object-contain"></div>
		<?php else : ?>
			<div><?php the_custom_logo() ?></div>
		<?php endif; ?>

	</div>

	<div class="mt-2.5">

		<?php wp_nav_menu([
			'menu_id' => 'mobile-menu',
			'menu_class' => 'gap-0.5 [&>li]:border-t [&>li]:border-[#F4F4F6] [&>li]:first:border-t-0 flex-col text-cynBlack [&_li_a]:flex [&_li_a]:py-3 [&_li_a]:w-full text-base font-medium [&_li_ul]:px-3',
			'depth' => '3',
			'theme_location' => 'mobile-menu',
			'container' => 'ul'
		]); ?>

	</div>

	<div class="flex gap-2 flex-col text-cynBlack text-sm font-medium mt-8 mb-12">

		<?php if ($whatsapp_number || $twitter_link || $telegram_link || $instagram_link || $pinterest_link) : ?>

			<p class="text-xs font-semibold"><?php _e('شبکه های اجتماعی', 'taghechian'); ?></p>

			<div class="flex gap-4">

				<?php if ($whatsapp_number) : ?>
					<a href="<?php echo $whatsapp_number ?>" class="bg-cynBG rounded-xl text-cynBlack p-2 flex items-center gap-1">
						<i class="size-6 flex items-center justify-center text-[#22C55E] stroke-2">
							<?php Icon::print('Whatsup'); ?>
						</i>
					</a>
				<?php endif; ?>

				<?php if ($twitter_link) : ?>
					<a href="<?php echo $twitter_link ?>" class="bg-cynBG rounded-xl text-cynBlack p-2 flex items-center gap-1">
						<i class="size-5 flex items-center justify-center text-cynBlack stroke-2 p-0.5">
							<?php get_template_part('assets/image/x'); ?>
						</i>
					</a>
				<?php endif; ?>

				<?php if ($telegram_link) : ?>
					<a href="<?php echo $telegram_link ?>" class="bg-cynBG rounded-xl text-cynBlack p-2 flex items-center gap-1">
						<i class="size-6 flex items-center justify-center text-[#00BFE1] stroke-2">
							<?php Icon::print('Telegram'); ?>
						</i>
					</a>
				<?php endif; ?>

				<?php if ($instagram_link) : ?>
					<a href="<?php echo $instagram_link ?>" class="bg-cynBG rounded-xl text-cynBlack p-2 flex items-center gap-1">
						<i class="size-6 flex items-center justify-center text-[#DB2777] stroke-2">
							<?php Icon::print('Instagram'); ?>
						</i>
					</a>
				<?php endif; ?>

				<?php if ($pinterest_link) : ?>
					<a href="<?php echo $pinterest_link ?>" class="bg-cynBG rounded-xl text-cynBlack p-2 flex items-center gap-1">
						<i class="size-6 flex items-center justify-center text-[#E60023] stroke-2">
							<?php Icon::print('Pinterest'); ?>
						</i>
					</a>
				<?php endif; ?>

			</div>
		<?php endif; ?>
	</div>

</section>