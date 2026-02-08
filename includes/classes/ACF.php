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
}
