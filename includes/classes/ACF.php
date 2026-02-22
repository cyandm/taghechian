<?php

/**
 * ACF Class
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

use Cyan\Theme\Helpers\Validators;
use Cyan\Theme\Helpers\ACF\AcfGroup;


class ACF
{

	public static function init()
	{
		$isDev = ENVIRONMENT === 'development';
		$isDev ? null : add_filter('acf/settings/show_admin', '__return_false', 100);

		if (! function_exists('acf_add_local_field_group')) {
			return;
		}


		add_action('acf/include_fields', [__CLASS__, 'registerAllACF']);
	}

	/**
	 * Register all ACF fields for the individual post types, taxonomies, page templates, and menu items
	 * @return void
	 */
	public static function registerAllACF()
	{
		self::forHome();
		self::forBranches();
		self::forContactUs();
		self::forAboutUs();
		self::forOrganizational();
		self::forArchiveBlog();
		self::forGiftCard();
		self::forProduct();
		self::forShop();
	}

	private static function forHome()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('home_faq', 'سوالات متداول');
		$acfGroup->basicFields->addText('home_faq_title', 'عنوان سوالات متداول', ['placeholder' => 'سوالات متداول', 'width' => '50%']);
		$acfGroup->basicFields->addText('home_faq_under_title', 'متن زیر عنوان سوالات متداول', ['placeholder' => 'متن زیر عنوان سوالات متداول', 'width' => '50%']);
		$acfGroup->relationshipFields->addLink('home_faq_button', 'لینک و متن دکمه سوالات متداول (اگر این قسمت پر نشود شماره تلفن  پشتیبانی نمایش داده خواهد شد)', ['width' => '100%']);

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/home.php');

		// register group
		$acfGroup->register('Branch');
	}

	private static function forBranches()
	{

		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->basicFields->addText('branch_phone', 'شماره تماس شعبه', [
			'width' => '50%',
		]);

		$acfGroup->basicFields->addText('branch_address', 'آدرس شعبه', [
			'width' => '50%',
		]);

		$location_fields = [
			'branch_loc_link_balad' => 'لینک مکان یاب بلد',
			'branch_loc_link_neshan' => 'لینک مکان یاب نشان',
			'branch_loc_link_snapp' => 'لینک مکان یاب اسنپ',
			'branch_loc_link_tapsi' => 'لینک مکان یاب تپسی',
			'branch_loc_link_google' => 'لینک مکان یاب گوگل',
			'branch_loc_link_waze' => 'لینک مکان یاب ویز',
		];

		foreach ($location_fields as $field => $label) {
			$acfGroup->basicFields->addText($field, $label, [
				'width' => '20%',
			]);
		}

		//location
		$acfGroup->setLocation('post_type', '==', 'branch');

		// register group
		$acfGroup->register('Branch');
	}

	private static function forContactUs()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->basicFields->addText('contact_form_title', 'عنوان', ['width' => '50%']);

		$acfGroup->basicFields->addTextarea('contact_form_under_title', 'متن زیر عنوان', ['width' => '50%']);

		$acfGroup->basicFields->addText('contact_branches_title', 'عنوان شعب', ['width' => '50%']);

		$acfGroup->basicFields->addTextarea('contact_branches_under_title', 'متن زیر عنوان شعب', ['width' => '50%']);

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/contact-us.php');

		//register group
		$acfGroup->register('Contact-Us');
	}

	private static function forAboutUs()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('aboutUs_hero_tab', 'ویدیو یا تصویر اول صفحه');
		$acfGroup->contentFields->addFile('aboutUs_file', 'لطفا یک فایل انتخاب کنید', ['width' => '50%']);
		$acfGroup->contentFields->addImage('aboutUs_video_cover', 'اگر ویدیو انتخاب نمودید، لطفا یک تصویر جهت نمایش کاور ویدیو هم انتخاب کنید', ['width' => '50%']);
		$acfGroup->basicFields->addText('aboutUs_video_cover_text', 'متن روی کاور');

		$acfGroup->layoutFields->addTab('aboutUs_first_section_tab', 'توضیحات بخش اول');
		$acfGroup->basicFields->addText('aboutUs_first_section_title', 'عنوان', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addTextEditor('aboutUs_first_section_description', 'توضیحات', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addImage('aboutUs_first_section_image', 'تصویر', ['width' => '50%', 'required' => 1]);

		$acfGroup->layoutFields->addTab('aboutUs_second_section_tab', 'توضیحات بخش دوم');
		$acfGroup->basicFields->addText('aboutUs_second_section_title', 'عنوان', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addTextEditor('aboutUs_second_section_description', 'توضیحات', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addImage('aboutUs_second_section_image', 'تصویر', ['width' => '50%', 'required' => 1]);

		$acfGroup->layoutFields->addTab('aboutUs_gallery_tab', 'گالری');
		$acfGroup->basicFields->addText('aboutUs_gallery_title', 'عنوان گالری', ['width' => '50%']);
		$acfGroup->basicFields->addText('aboutUs_gallery_under_title', 'متن زیر عنوان گالری', ['width' => '50%']);

		for ($i = 1; $i <= 30; $i++) {
			$acfGroup->contentFields->addImage('aboutUs_gallery_image_' . $i, 'تصویر ' . $i, ['width' => '50%']);
		}

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/about-us.php');

		//register group
		$acfGroup->register('About-us');
	}

	private static function forOrganizational()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('organizational_image_tab', 'تصویر ابتدا صفحه');
		$acfGroup->contentFields->addImage('organizational_image', 'لطفا یک تصویر انتخاب کنید (1280x542px یا دو برابر)', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('organizational_first_section_tab', 'توضیحات بخش اول');
		$acfGroup->basicFields->addText('organizational_first_section_title', 'عنوان', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addTextEditor('organizational_first_section_description', 'توضیحات', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addImage('organizational_first_section_image', 'تصویر', ['width' => '50%', 'required' => 1]);

		$acfGroup->layoutFields->addTab('organizational_cooperation_process_tab', 'فرایند همکاری');
		$acfGroup->basicFields->addText('organizational_cooperation_process_section_title', 'عنوان بخش', ['width' => '50%']);
		for ($i = 1; $i <= 4; $i++) {
			$acfGroup->basicFields->addText('organizational_cooperation_process_title_' . $i, 'عنوان ' . $i, ['width' => '50%']);
		}

		$acfGroup->layoutFields->addTab('organizational_second_section_tab', 'توضیحات بخش دوم');
		$acfGroup->basicFields->addText('organizational_second_section_title', 'عنوان', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addTextEditor('organizational_second_section_description', 'توضیحات', ['width' => '50%', 'required' => 1]);
		$acfGroup->contentFields->addImage('organizational_second_section_image', 'تصویر', ['width' => '50%', 'required' => 1]);

		$acfGroup->layoutFields->addTab('organizational_form_tab', 'فرم سفارش');
		$acfGroup->basicFields->addText('organizational_form_title', 'عنوان', ['width' => '50%']);
		$acfGroup->basicFields->addTextarea('organizational_form_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addImage('organizational_form_image', 'تصویر', ['width' => '50%']);

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/organizational.php');

		//register group
		$acfGroup->register('Organizational');
	}

	private static function forArchiveBlog()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('archive_blog_event_tab', 'رویدادها');
		$acfGroup->basicFields->addText('archive_blog_event_title', 'عنوان رویدادها', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_event_under_title', 'متن زیر عنوان رویدادها', ['width' => '50%']);
		$acfGroup->contentFields->addImage('archive_blog_event_image', 'تصویر رویدادها', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('archive_blog_section_one_tab', 'بخش اول');
		$acfGroup->basicFields->addText('archive_blog_section_one_title', 'عنوان بخش اول', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_one_under_title', 'متن زیر عنوان بخش اول', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_one_english_title', 'عنوان بخش اول به زبان انگلیسی', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_one_english_under_title', 'متن زیر عنوان بخش اول به زبان انگلیسی', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('archive_blog_section_two_tab', 'بخش دوم');
		$acfGroup->basicFields->addText('archive_blog_section_two_title', 'عنوان بخش دوم', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_two_under_title', 'متن زیر عنوان بخش دوم', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_two_english_title', 'عنوان بخش دوم به زبان انگلیسی', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_two_english_under_title', 'متن زیر عنوان بخش دوم به زبان انگلیسی', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('archive_show_tab', 'فشن شو');
		$acfGroup->basicFields->addText('archive_show_title', 'عنوان فشن شو', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_show_under_title', 'متن زیر عنوان فشن شو', ['width' => '50%']);

		for ($i = 1; $i <= 4; $i++) {
			$acfGroup->contentFields->addImage('archive_show_image_' . $i, 'تصویر فشن شو ' . $i, ['width' => '50%']);
		}

		$acfGroup->layoutFields->addTab('archive_blog_section_three_tab', 'بخش سوم');
		$acfGroup->basicFields->addText('archive_blog_section_three_title', 'عنوان بخش سوم', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_three_under_title', 'متن زیر عنوان بخش سوم', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_three_english_title', 'عنوان بخش سوم به زبان انگلیسی', ['width' => '50%']);
		$acfGroup->basicFields->addText('archive_blog_section_three_english_under_title', 'متن زیر عنوان بخش سوم به زبان انگلیسی', ['width' => '50%']);
		//location
		$acfGroup->setLocation('page_template', '==', 'templates/archive-blog.php');

		//register group
		$acfGroup->register('Archive-Blog');
	}

	private static function forGiftCard()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('gift_card_tab', 'متن و تصویر ابتدا صفحه');
		$acfGroup->basicFields->addText('gift_card_title', 'عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addTextEditor('gift_card_desc', 'توضیحات', ['width' => '50%']);
		$acfGroup->relationshipFields->addLink('gift_card_button', 'لینک و متن دکمه', ['width' => '50%']);
		$acfGroup->contentFields->addImage('gift_card_image', 'تصویر', ['width' => '50%']);

		$acfGroup->layoutFields->addTab('gift_card_show_tab', 'کارت های هدیه');
		$acfGroup->basicFields->addText('gift_card_show_title', 'عنوان', ['width' => '50%']);
		$acfGroup->basicFields->addText('gift_card_show_under_title', 'متن زیر عنوان', ['width' => '50%']);
		$acfGroup->relationshipFields->addPostObject('gift_card_show_post', 'انتخاب کارت ها جهت نمایش (اگر کارتی انتخاب نشود همه کارت ها نمایش داده خواهد شد)', ['post_type' => 'product', 'multiple' => 1, 'width' => '50%']);

		$acfGroup->layoutFields->addTab('gift_card_box_tab', 'متن و تصویر انتهای صفحه');
		$acfGroup->basicFields->addText('gift_card_box_title', 'عنوان', ['width' => '50%']);
		$acfGroup->contentFields->addTextEditor('gift_card_box_desc', 'توضیحات', ['width' => '50%']);
		$acfGroup->contentFields->addImage('gift_card_box_image', 'تصویر', ['width' => '50%']);

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/gift-card.php');

		//register group
		$acfGroup->register('Gift-Card');
	}

	private static function forProduct()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('product_certificate_tab', 'شناسنامه محصول');
		$acfGroup->contentFields->addTextEditor('product_certificate', 'متن یا توضیحات شناسنامه محصول', ['width' => '50%', 'toolbar' => 'advanced']);

		$acfGroup->layoutFields->addTab('product_details_tab', 'جزئیات محصول');
		$acfGroup->contentFields->addTextEditor('product_details', 'متن یا توضیحات جزئیات محصول', ['width' => '50%', 'toolbar' => 'advanced']);

		$acfGroup->layoutFields->addTab('product_care_tab', 'نکات نگهداری');
		$acfGroup->contentFields->addTextEditor('product_care', 'متن یا توضیحات نکات نگهداری', ['width' => '50%', 'toolbar' => 'advanced']);

		$acfGroup->layoutFields->addTab('product_gift_card_tab', '(اگر محصول کارت هدیه است لطفا اینجا را تکمیل کنید) کارت هدیه');
		$acfGroup->basicFields->addText('product_gift_card_expire', 'تاریخ استفاده', ['width' => '50%', 'placeholder' => 'تا یک سال پس از خرید']);
		$acfGroup->basicFields->addText('product_gift_card_use', 'قابل استفاده', ['width' => '50%', 'placeholder' => 'برای خرید لباس، آرت دکور و...']);


		//location
		$acfGroup->setLocation('post_type', '==', 'product');

		//register group
		$acfGroup->register('Product');
	}

	private static function forShop()
	{
		//define helper
		$acfGroup = new AcfGroup();

		//add fields
		$acfGroup->layoutFields->addTab('shop_tab', 'ویدیوهای کالکشن ابتدای صفحه');

		for ($i = 1; $i <= 3; $i++) {
			$acfGroup->contentFields->addFile('shop_video_' . $i, 'ویدیو ' . $i, ['width' => '30%']);
			$acfGroup->contentFields->addImage('shop_video_cover_' . $i, 'تصویر کاور ویدیو ' . $i, ['width' => '30%']);
			$acfGroup->basicFields->addText('shop_video_title_' . $i, 'عنوان ویدیو ' . $i, ['width' => '30%']);
		}

		$acfGroup->layoutFields->addTab('shop_first_section_tab', 'سکشن محصولات اول');
		$acfGroup->basicFields->addText('shop_first_section_title', 'عنوان', ['width' => '33%']);
		$acfGroup->basicFields->addText('shop_first_section_under_title', 'متن زیر عنوان', ['width' => '33%']);
		$acfGroup->relationshipFields->addLink('shop_first_section_button', 'لینک و متن دکمه', ['width' => '33%']);
		$acfGroup->basicFields->addText('shop_first_section_persian_title', 'عنوان روی اسلایدر به زبان فارسی', ['width' => '50%']);
		$acfGroup->basicFields->addText('shop_first_section_english_title', 'عنوان روی اسلایدر به زبان انگلیسی', ['width' => '50%']);
		$acfGroup->relationshipFields->addPostObject('shop_first_section_post', 'انتخاب محصولات (اگر محصولی انتخاب نشود محصولات جدید نمایش داده خواهد شد)', ['post_type' => 'product', 'multiple' => 1, 'width' => '100%']);

		$acfGroup->layoutFields->addTab('shop_banner_one_tab', 'بنر اول');
		$acfGroup->contentFields->addImage('shop_banner_one_image_desktop', 'تصویر بنر برای دسکتاپ', ['width' => '33%']);
		$acfGroup->contentFields->addImage('shop_banner_one_image_mobile', 'تصویر بنر برای موبایل', ['width' => '33%']);
		$acfGroup->basicFields->addUrl('shop_banner_one_link', 'لینک بنر (در صورت خالی بودن بنر بدون لینک درج میشود)', ['width' => '33%']);

		$acfGroup->layoutFields->addTab('shop_second_section_tab', 'سکشن محصولات دوم');
		$acfGroup->basicFields->addText('shop_second_section_title', 'عنوان', ['width' => '20%']);
		$acfGroup->basicFields->addText('shop_second_section_under_title', 'متن زیر عنوان', ['width' => '20%']);
		$acfGroup->relationshipFields->addLink('shop_second_section_button', 'لینک و متن دکمه', ['width' => '20%']);
		$acfGroup->basicFields->addText('shop_second_section_persian_title', 'عنوان روی اسلایدر به زبان فارسی', ['width' => '20%']);
		$acfGroup->basicFields->addText('shop_second_section_english_title', 'عنوان روی اسلایدر به زبان انگلیسی', ['width' => '20%']);
		$acfGroup->relationshipFields->addPostObject('shop_second_section_post', 'انتخاب محصولات (اگر محصولی انتخاب نشود محصولات جدید نمایش داده خواهد شد)', ['post_type' => 'product', 'multiple' => 1, 'width' => '100%']);

		$acfGroup->layoutFields->addTab('shop_banner_two_tab', 'بنر دوم');
		$acfGroup->contentFields->addImage('shop_banner_two_image_desktop', 'تصویر بنر برای دسکتاپ', ['width' => '33%']);
		$acfGroup->contentFields->addImage('shop_banner_two_image_mobile', 'تصویر بنر برای موبایل', ['width' => '33%']);
		$acfGroup->basicFields->addUrl('shop_banner_two_link', 'لینک بنر (در صورت خالی بودن بنر بدون لینک درج میشود)', ['width' => '33%']);

		$acfGroup->layoutFields->addTab('shop_third_section_tab', 'سکشن محصولات سوم');
		$acfGroup->basicFields->addText('shop_third_section_title', 'عنوان', ['width' => '20%']);
		$acfGroup->basicFields->addText('shop_third_section_under_title', 'متن زیر عنوان', ['width' => '20%']);
		$acfGroup->relationshipFields->addLink('shop_third_section_button', 'لینک و متن دکمه', ['width' => '20%']);
		$acfGroup->basicFields->addText('shop_third_section_persian_title', 'عنوان روی اسلایدر به زبان فارسی', ['width' => '20%']);
		$acfGroup->basicFields->addText('shop_third_section_english_title', 'عنوان روی اسلایدر به زبان انگلیسی', ['width' => '20%']);
		$acfGroup->relationshipFields->addPostObject('shop_third_section_post', 'انتخاب محصولات (اگر محصولی انتخاب نشود محصولات جدید نمایش داده خواهد شد)', ['post_type' => 'product', 'multiple' => 1, 'width' => '100%']);

		//location
		$acfGroup->setLocation('page_template', '==', 'templates/shop.php');

		//register group
		$acfGroup->register('Information');
	}
}
