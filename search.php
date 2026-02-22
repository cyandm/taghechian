<?php

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

defined('ABSPATH') || exit;

global $wp_query;

$search_type = empty($_GET['search-type']) ? 'all' : $_GET['search-type'];

?>

<?php get_header() ?>

<?php Templates::getPart('breadcrumb'); ?>

<main id="search-page" class="flex flex-col gap-6">

    <section class="bg-cynWhite">

        <hr class="border-cynBgItem/30 h-px w-full mb-6 max-md:hidden">

        <div id="searchPostType" class="container">

            <form id="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="flex justify-between items-center max-md:flex-col max-md:gap-6 max-md:px-4 max-md:py-5 max-md:border border-cynBlack/10 rounded-3xl">

                <div class="relative max-md:w-full">

                    <div class="absolute inset-0 top-1/2 -translate-y-1/2 flex items-center ps-3.5 pointer-events-none text-cynYellow size-9">
                        <?php Icon::print('Search,-Loupe'); ?>
                    </div>

                    <input type="text"
                        id="email-address-icon"
                        name="s"
                        value="<?php the_search_query() ?>"
                        class="text-cynBlack text-base font-medium rounded-2xl border border-cynYellow focus:outline-none focus:ring-cynYellow focus:border-cynYellow block w-full ps-10 p-3 pt-3.5 transition-all duration-200 md:min-w-64"
                        placeholder="<?php _e('دنبال چی میگردی؟', 'taghechian'); ?>">
                </div>

                <div class="flex justify-between items-center gap-2 md:gap-6 text-base font-medium max-sm:w-full">

                    <div class="text-base font-medium text-cynBlack">
                        <?php _e('جستجو در', 'taghechian'); ?> :
                    </div>

                    <div class="flex gap-4">
                        <div class="flex justify-center items-center gap-1">
                            <input class="peer custom filter w-6 h-6 accent-cynYellow"
                                value="all"
                                type="radio"
                                name="search-type"
                                id="search-all"
                                <?php echo $search_type === 'all' ? 'checked' : '' ?>>
                            <label for="search-all" class="text-sm font-medium text-cynBlack/50 peer-checked:text-cynBlack cursor-pointer"><?php _e('همه', 'taghechian'); ?></label>
                        </div>

                        <div class="flex justify-center items-center gap-1">
                            <input class="peer custom filter w-6 h-6 accent-cynYellow"
                                value="product"
                                type="radio"
                                name="search-type"
                                id="search-product"
                                <?php echo $search_type === 'product' ? 'checked' : '' ?>>
                            <label for="search-product" class="text-sm font-medium text-cynBlack/50 peer-checked:text-cynBlack cursor-pointer"><?php _e('فروشگاه', 'taghechian'); ?></label>
                        </div>

                        <div class="flex justify-center items-center gap-1">
                            <input class="peer custom filter w-6 h-6 accent-cynYellow"
                                value="post"
                                type="radio"
                                name="search-type"
                                id="search-blog"
                                <?php echo $search_type === 'post' ? 'checked' : '' ?>>
                            <label for="search-blog" class="text-sm font-medium text-cynBlack/50 peer-checked:text-cynBlack cursor-pointer"><?php _e('بلاگ', 'taghechian'); ?></label>
                        </div>

                    </div>
                </div>

                <div class="h-px bg-cynBlack/10 w-full md:hidden"></div>

                <?php if (! empty($_GET['s'])) : ?>
                    <div class="flex items-center gap-1 text-base font-light text-cynBlack/60">
                        <span><?php echo (int) $wp_query->found_posts; ?></span>
                        <span><?php _e('نتیجه', 'taghechian'); ?></span>
                    </div>
                <?php endif ?>
            </form>

        </div>

        <hr class="border-cynBgItem/30 h-px w-full mt-6 max-md:hidden">

    </section>

    <section class="container">
        <?php if (! empty($_GET['s'])) : ?>

            <?php if (have_posts()) : ?>

                <div id="searchPostsWrapper" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <?php while (have_posts()) :
                        the_post();
                        $is_product = (get_post_type() === 'product');
                    ?>
                        <div class="<?php echo $is_product ? '' : 'sm:col-span-2'; ?>">
                            <?php if ($is_product) : ?>
                                <?php Templates::getCard('search-product'); ?>
                            <?php else : ?>
                                <?php Templates::getCard('search-blog'); ?>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php
                $search_total   = (int) $wp_query->max_num_pages;
                $search_current = max(1, (int) get_query_var('paged'));
                if ($search_total > 1 && function_exists('wc_get_template')) {
                    wc_get_template('loop/pagination.php', [
                        'total'   => $search_total,
                        'current' => $search_current,
                    ]);
                }
                ?>

            <?php
            else:
                Templates::getCard('search-not-found');
            endif;
            ?>

        <?php endif; ?>
    </section>

</main>

<?php get_footer() ?>