<?php

/**
 * Setup
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

class Setup
{

	public static function init()
	{

		ThemeInit::init();

		ThirdParty::init();

		Customize::init();

		CustomCode::init();

		Register::init();

		Search::init();

		Rest::init();

		Meta::init();

		SingleGiftCard::init();

		ProductAttributes::init();

		WooCommerce::init();
	}
}
