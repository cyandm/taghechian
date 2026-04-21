<?php

/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.3.0
 */

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Icon;

global $product;

if (! comments_open()) {
	return;
}

// Enqueue comment-reply script for threaded comments
if (is_singular() && comments_open() && get_option('thread_comments')) {
	wp_enqueue_script('comment-reply');
}

// Get product average rating
$average_rating = $product->get_average_rating(); // e.g. 4.2
$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count(); // Number of reviews

// Set default rating if no reviews
// if ($average_rating == 0) {
// 	$average_rating = 5; // Or any default value you want
// }

// Calculate full and half stars
$full_stars = floor($average_rating); // Full stars
$half_star = ($average_rating - $full_stars) * 100; // Half star percentage
$empty_stars = 5 - ceil($average_rating); // Empty stars

// Custom comment callback function
function taghechian_product_review_callback($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;
	$rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
	$is_reply = $comment->comment_parent ? true : false;
?>
	<li <?php comment_class($is_reply ? 'mb-4' : 'mb-3'); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-container <?php echo $is_reply ? 'bg-cynWhite [&_img]:size-7' : 'bg-cynBlack/4 md:p-6'; ?> p-4 rounded-3xl">
			<div class="flex gap-3 items-start">
				<!-- Avatar -->
				<div class="flex-shrink-0 flex gap-1 justify-center items-center">

					<?php if ($is_reply): ?>

						<i class="size-5 text-cynBlack/70 pointer-events-none">
							<?php Icon::print('share-arrow') ?>
						</i>

					<?php endif ?>

					<?php echo get_avatar($comment, 48, '', '', array('class' => 'rounded-full w-12 h-12')); ?>
				</div>

				<!-- Comment Content -->
				<div class="flex-1">
					<!-- Author Name and Date -->
					<div class="flex items-center justify-between mb-2">
						<div class="flex items-center gap-2">
							<strong class="text-base font-semibold text-cynBlack"><?php comment_author(); ?></strong>

							<?php
							$comment_user_email = $comment->comment_author_email;
							$comment_user_id    = $comment->user_id;
							$product_id         = get_the_ID();

							$is_buyer = wc_customer_bought_product(
								$comment_user_email,
								$comment_user_id,
								$product_id
							);
							?>

							<?php if ($is_buyer): ?>
								<span class="bg-gray-200 text-cynBlack text-xs px-3 py-1 rounded-lg">
									خریدار
								</span>
							<?php endif; ?>

							<!-- Reply/Share Icon -->
							<?php if (!$is_reply) :
								$reply_link = get_comment_reply_link(array_merge($args, array(
									'depth'     => $depth,
									'max_depth' => $args['max_depth'],
									'reply_text' => Icon::get('Reply,-Share,-Circle'),
									'before'    => '',
									'after'     => '',
								)));
								if ($reply_link) {
									// Replace <a> tag classes
									$reply_link = str_replace('class="comment-reply-link"', 'class="text-cynBlack/70 hover:text-cynBlack transition-colors size-5 inline-block"', $reply_link);
									echo $reply_link;
								}
							endif; ?>
						</div>
					</div>

					<!-- Date and Time -->
					<div class="text-sm text-gray-500 mb-2">
						<?php
						$comment_date = get_comment_date('j F Y', $comment->comment_ID);
						$comment_time = get_comment_time('H:i', false, true);
						echo $comment_date . '، ' . $comment_time;
						?>
					</div>

					<!-- Rating Stars (only for main comments, not replies) -->
					<?php if ($rating && wc_review_ratings_enabled() && !$is_reply) : ?>
						<div class="mb-2 flex gap-1">
							<?php
							for ($i = 1; $i <= 5; $i++) {
								if ($i <= $rating) {
									echo '<span class="text-cynStars text-lg">★</span>';
								} else {
									echo '<span class="text-gray-300 text-lg">★</span>';
								}
							}
							?>
						</div>
					<?php endif; ?>

					<!-- Comment Text -->
					<div class="text-base text-cynBlack mb-2">
						<?php comment_text(); ?>
					</div>

					<!-- Reply Link (only show for main comments, not for nested replies) -->
					<?php if (!$is_reply) :
						comment_reply_link(array_merge($args, array(
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="reply-link mt-2 text-[#EFA114] text-sm font-medium cursor-pointer">',
							'after'     => '</div>',
							'reply_text' => __('پاسخ', 'taghechian'),
						)));
					endif;
					?>
				</div>
			</div>
			<!-- Don't close comment-container div yet - children will go inside -->
		<?php
	}

	// Function to close the comment tag properly
	function taghechian_product_review_end_callback($comment, $args, $depth)
	{
		?>
		</div><!-- Close comment-container -->
	</li>
<?php
	}

	$comment_count = get_comments_number();
?>

<div id="reviews" class="woocommerce-Reviews w-full flex gap-4 max-lg:flex-col-reverse mt-5">

	<div id="comments" class="lg:w-5/9 xl:w-3/5">
		<?php if (have_comments()) : ?>
			<ol class="commentlist list-none p-0 m-0">
				<?php
				wp_list_comments(array(
					'callback' => 'taghechian_product_review_callback',
					'end-callback' => 'taghechian_product_review_end_callback',
					'style'    => 'ol',
					'avatar_size' => 48,
					'max_depth' => 2, // Allow one level of replies
					'per_page' => -1, // Show all comments
				));
				?>
			</ol>

			<?php
			if (get_comment_pages_count() > 1 && get_option('page_comments')) :
				echo '<nav class="woocommerce-pagination mt-6">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
							'next_text' => is_rtl() ? '&larr;' : '&rarr;',
							'type' => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php else: ?>

			<div class="comment-container bg-cynBlack/4 px-4 py-11 md:px-6 rounded-3xl font-medium text-base md:text-xl text-cynBlack/80 text-center">
				اولین دیدگاه این محصول رو تو ثبت کن!
			</div>

		<?php endif; ?>
	</div>

	<div id="review_form_wrapper" class="lg:w-4/9 xl:w-2/5 relative">
		<div id="review_form" class="sticky top-4 pb-3">

			<div class="flex flex-col gap-5 mb-5">
				<div class="p-6 bg-[#f8f8f8] rounded-3xl flex flex-col gap-6 justify-center">

					<p class="text-[28px] font-[dinar] font-medium text-cynBlack/80"><?php _e('دیدگاه و امتیاز خریداران', 'taghechian') ?></p>

					<div class="flex gap-1 justify-end items-center">
						<span class="text-green-600 font-semibold text-xl">
							<?php echo $average_rating; ?>
						</span>
						<span class="text-cynBlack font-medium text-sm">
							از 5
						</span>
					</div>

					<div class="mb-4 flex items-center gap-1.5 justify-end">

						<span class="text-sm text-cynBlack pt-px font-normal">
							(از مجموع امتیاز <?php echo $rating_count; ?> خریدار)
						</span>

						<div class="w-px h-6 bg-gray-300"></div>

						<span class="flex items-center justify-center gap-0.5">
							<?php
							for ($i = 1; $i <= 5; $i++):
								$fill_percentage = 0;

								if ($i <= $full_stars) {
									$fill_percentage = 100;
								} elseif ($i == $full_stars + 1) {
									$fill_percentage = $half_star;
								} else {
									$fill_percentage = 0;
								}

								$gradient_id = 'reviewStarGradient' . $i;
							?>
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 18 18">
									<defs>
										<linearGradient id="<?php echo $gradient_id; ?>" x1="100%" y1="0%" x2="0%" y2="0%">
											<stop offset="<?php echo $fill_percentage; ?>%" stop-color="#fecf00" />
											<stop offset="<?php echo $fill_percentage; ?>%" stop-color="#E0E0E0" />
										</linearGradient>
									</defs>
									<path d="M16.963,6.786c-.088-.271-.323-.469-.605-.51l-4.62-.671L9.672,1.418c-.252-.512-1.093-.512-1.345,0l-2.066,4.186-4.62,.671c-.282,.041-.517,.239-.605,.51-.088,.271-.015,.57,.19,.769l3.343,3.258-.79,4.601c-.048,.282,.067,.566,.298,.734,.231,.167,.538,.189,.79,.057l4.132-2.173,4.132,2.173c.11,.058,.229,.086,.349,.086,.155,0,.31-.048,.441-.143,.231-.168,.347-.452,.298-.734l-.79-4.601,3.343-3.258c.205-.199,.278-.498,.19-.769Z" fill="url(#<?php echo $gradient_id; ?>)" />
								</svg>
							<?php endfor ?>
						</span>

					</div>
				</div>

				<div class="flex flex-col justify-center items-center">
					<p class="text-base font-medium text-center text-cynBlack/70"><?php _e('شما هم از تجربه خریدتون برامون بنویسین!', 'taghechian'); ?></p>
				</div>

			</div>

			<?php
			$commenter = wp_get_current_commenter();
			$comment_form = array(
				'title_reply' => '',
				'title_reply_to' => __('پاسخ به %s', 'taghechian'),
				'title_reply_before' => '',
				'title_reply_after' => '',
				'comment_notes_after' => '',
				'cancel_reply_before' => '',
				'cancel_reply_after' => '',
				'cancel_reply_link' => __('لغو پاسخ', 'taghechian'),
				'label_submit' => __('افزودن دیدگاه', 'taghechian'),
				'logged_in_as' => '',
				'comment_field' => '',
				'class_submit' => 'primary-btn text-white flex items-center justify-center gap-2 !py-3 !px-6 text-base font-medium text-cynBlack w-full',
				'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s<span class="size-6 stroke-[1.5]">' . Icon::get('Messages,-Chat-18') . '</span></button>',
			);

			$name_email_required = (bool) get_option('require_name_email', 1);
			$fields = array(
				'author' => array(
					'label' => __('نام شما', 'taghechian'),
					'type' => 'text',
					'value' => $commenter['comment_author'],
					'required' => $name_email_required,
				),
				'email' => array(
					'label' => __('ایمیل شما', 'taghechian'),
					'type' => 'email',
					'value' => $commenter['comment_author_email'],
					'required' => $name_email_required,
				),
			);

			$comment_form['fields'] = array();

			// Build name and email fields HTML
			$name_email_html = '';
			foreach ($fields as $key => $field) {
				$icon_name = $key === 'author' ? 'User,-Profile' : 'email-mail-letter';
				$icon_content = Icon::get($icon_name);
				$name_email_html .= '<div class="comment-form-' . esc_attr($key) . ' mb-4">';
				$name_email_html .= '<div class="relative">';
				if ($icon_content) {
					$name_email_html .= '<div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">';
					$name_email_html .= '<span class="size-7 text-gray-400">';
					$name_email_html .= $icon_content;
					$name_email_html .= '</span>';
					$name_email_html .= '</div>';
				}
				$name_email_html .= '<input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($field['value']) . '" placeholder="' . esc_attr($field['label']) . '" class="' . ($icon_content ? 'pr-11' : 'pr-3') . ' bg-white/50 rounded-2xl border border-cynBlack/20 text-base font-medium w-full pr-11 py-3 text-cynBlack focus:outline-none focus:ring-2 focus:ring-cynOrange resize-none transition-all duration-200" ' . ($field['required'] ? 'required' : '') . ' />';
				$name_email_html .= '</div>';
				$name_email_html .= '</div>';
			}

			$account_page_url = wc_get_page_permalink('myaccount');
			if ($account_page_url) {
				$comment_form['must_log_in'] = '<p class="must-log-in text-gray-500 mb-4">' . sprintf(esc_html__('برای ارسال دیدگاه باید %1$sوارد%2$s شوید.', 'taghechian'), '<a href="' . esc_url($account_page_url) . '" class="text-cynOrange">', '</a>') . '</p>';
			}

			// Start comment_field with name and email fields first
			$message_icon = Icon::get('Chat,-Messages-1');
			$comment_form['comment_field'] = $name_email_html;

			// Then add comment textarea
			$comment_form['comment_field'] .= '<div class="comment-form-comment">';
			$comment_form['comment_field'] .= '<div class="relative">';
			if ($message_icon) {
				$comment_form['comment_field'] .= '<div class="absolute top-3 right-3 pointer-events-none">';
				$comment_form['comment_field'] .= '<span class="size-7 text-gray-400">';
				$comment_form['comment_field'] .= $message_icon;
				$comment_form['comment_field'] .= '</span>';
				$comment_form['comment_field'] .= '</div>';
			}
			$comment_form['comment_field'] .= '<textarea id="comment" name="comment" placeholder="' . esc_attr__('نظر شما', 'taghechian') . '" rows="6" required class="bg-white/50 rounded-2xl border border-cynBlack/20 text-base font-medium w-full ' . ($message_icon ? 'pr-11' : 'pr-3') . ' py-3 text-cynBlack focus:outline-none focus:ring-2 focus:ring-cynOrange resize-none transition-all duration-200"></textarea>';
			$comment_form['comment_field'] .= '</div>';
			$comment_form['comment_field'] .= '</div>';

			// Rating field after comment textarea
			if (wc_review_ratings_enabled()) {
				// Hide default WooCommerce rating HTML
				add_filter('woocommerce_product_review_comment_form_args', function ($comment_form_args) {
					$comment_form_args['comment_field'] = isset($comment_form_args['comment_field']) ? $comment_form_args['comment_field'] : '';
					return $comment_form_args;
				}, 10);

				$comment_form['comment_field'] .= '<div class="comment-form-rating mb-4">';
				$comment_form['comment_field'] .= '<div class="flex items-center gap-2 mb-1">';
				$comment_form['comment_field'] .= '<span class="text-base font-medium text-cynBlack">' . __('ستاره دهید', 'taghechian') . '</span>';
				$comment_form['comment_field'] .= '</div>';
				$comment_form['comment_field'] .= '<div class="flex gap-1 items-center">';
				$comment_form['comment_field'] .= '<select name="rating" id="rating" required style="display:none !important;">';
				$comment_form['comment_field'] .= '<option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>';
				for ($i = 5; $i >= 1; $i--) {
					$comment_form['comment_field'] .= '<option value="' . $i . '">' . $i . '</option>';
				}
				$comment_form['comment_field'] .= '</select>';
				$comment_form['comment_field'] .= '<div class="star-rating flex gap-1" data-rating="0">';
				for ($i = 1; $i <= 5; $i++) {
					$comment_form['comment_field'] .= '<span class="star text-2xl cursor-pointer text-gray-300 hover:text-cynStars transition-colors" data-value="' . $i . '">★</span>';
				}
				$comment_form['comment_field'] .= '</div>';
				$comment_form['comment_field'] .= '</div>';
				$comment_form['comment_field'] .= '</div>';
			}

			comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
			?>
		</div>
	</div>

</div>

<style>
	/* Styling for nested comment replies */
	.commentlist {
		list-style: none !important;
		padding: 0 !important;
		margin: 0 !important;
	}

	.commentlist>li {
		list-style: none !important;
	}

	/* Children list inside comment-container */
	.comment-container>.children {
		list-style: none !important;
		padding: 0 !important;
		margin: 0 !important;
		margin-top: 1rem !important;
		padding-right: 0 !important;
	}

	.comment-container>.children>li {
		list-style: none !important;
		margin-bottom: 1.5rem !important;
	}

	.comment-container>.children>li:last-child {
		margin-bottom: 0 !important;
	}

	/* Hide default WooCommerce stars in rating area */
	.comment-form-rating .stars {
		display: none !important;
	}
</style>