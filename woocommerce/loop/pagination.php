<?php

/**
 * Pagination - circular buttons: prev/next yellow, current page black, others white with border
 *
 * @package CyanTheme
 * @version 9.3.0
 */

if (!defined('ABSPATH')) {
	exit;
}

use Cyan\Theme\Helpers\Icon;

$total   = isset($total) ? (int) $total : (int) wc_get_loop_prop('total_pages');
$current = isset($current) ? (int) $current : (int) wc_get_loop_prop('current_page');

if ($total <= 1) {
	return;
}

$link_for = function ($page) {
	return esc_url(get_pagenum_link($page, false));
};
?>

<nav class="woocommerce-pagination flex justify-center items-center gap-2 my-8" aria-label="<?php esc_attr_e('صفحه‌بندی محصولات', 'taghechian'); ?>">

	<!-- Previous -->
	<?php if ($current > 1) : ?>
		<a href="<?php echo esc_url($link_for($current - 1)); ?>" class="size-11 rounded-full bg-cynYellow hover:opacity-90 text-cynBlack flex items-center justify-center shrink-0 transition-opacity no-underline" aria-label="<?php esc_attr_e('صفحهٔ قبل', 'taghechian'); ?>">
			<i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
		</a>
	<?php else : ?>
		<span class="size-11 rounded-full bg-cynYellow/50 text-cynBlack/60 flex items-center justify-center shrink-0 cursor-not-allowed" aria-hidden="true">
			<i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
		</span>
	<?php endif; ?>

	<!-- Page numbers -->
	<?php
	$show_pages = 2;
	$dots_before = false;
	$dots_after = false;
	for ($i = 1; $i <= $total; $i++) :
		$show = ($i === 1 || $i === $total || ($i >= $current - $show_pages && $i <= $current + $show_pages));
		if ($show) :
			if ($i > $current) $dots_after = false;
			if ($i < $current) $dots_before = false;
			if ($i === $current) : ?>
				<span class="size-11 rounded-full bg-cynBlack text-white flex items-center justify-center shrink-0 font-medium shadow-md min-w-[2.75rem]"><?php echo (int) $i; ?></span>
			<?php else : ?>
				<a href="<?php echo esc_url($link_for($i)); ?>" class="size-11 rounded-full bg-white border border-[#E5E5E5] text-cynBlack hover:border-cynBlack/30 flex items-center justify-center shrink-0 font-medium transition-colors no-underline min-w-[2.75rem]"><?php echo (int) $i; ?></a>
			<?php endif;
		else :
			if ($i < $current && !$dots_before) {
				$dots_before = true;
				echo '<span class="text-cynBlack/50 px-1">…</span>';
			} elseif ($i > $current && !$dots_after) {
				$dots_after = true;
				echo '<span class="text-cynBlack/50 px-1">…</span>';
			}
		endif;
	endfor;
	?>

	<!-- Next -->
	<?php if ($current < $total) : ?>
		<a href="<?php echo esc_url($link_for($current + 1)); ?>" class="size-11 rounded-full bg-cynYellow hover:opacity-90 text-cynBlack flex items-center justify-center shrink-0 transition-opacity no-underline" aria-label="<?php esc_attr_e('صفحهٔ بعد', 'taghechian'); ?>">
			<i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
		</a>
	<?php else : ?>
		<span class="size-11 rounded-full bg-cynYellow/50 text-cynBlack/60 flex items-center justify-center shrink-0 cursor-not-allowed" aria-hidden="true">
			<i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
		</span>
	<?php endif; ?>

</nav>
