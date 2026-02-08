<?php

/**
 * Header for wordpress theme
 * its must include only head and body tags
 * header templates located in /partials/header/
 * @package CyanTheme
 */

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$args = $args ?? [];
$render_template = $args['render_template'] ?? true;
$GLOBALS['cyan_header_light'] = !empty($args['header_light']);
?>
<!DOCTYPE html>
<?php
// Determine text direction based on current language
$current_lang = 'fa'; // Default to Persian
if (function_exists('pll_current_language')) {
	$current_lang = pll_current_language();
} elseif (function_exists('icl_get_languages')) {
	// WPML fallback
	$current_lang = ICL_LANGUAGE_CODE;
} else {
	// WordPress locale fallback
	$locale = get_locale();
	if (strpos($locale, 'en') === 0) {
		$current_lang = 'en';
	}
}
$text_direction = ($current_lang === 'en') ? 'ltr' : 'rtl';
?>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr($text_direction); ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body class="overflow-x-hidden bg-cynBG">
	<?php wp_body_open(); ?>

	<?php if ($render_template) : ?>

		<div class="icon hidden size-6" id="chevron-down">
			<?php Icon::print('Arrow-28') ?>
		</div>

		<header class="relative z-50">
			<?php Templates::getPart('desktop-header'); ?>
			<?php Templates::getPart('mobile-header'); ?>
		</header>
	<?php endif; ?>