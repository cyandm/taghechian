<?php

use Cyan\Theme\Helpers\Icon;
?>

<section class="mobile-menu-filter-scroll pb-24 h-screen bg-white p-5 w-0 fixed inset-0 z-50 opacity-0 pointer-events-none overflow-y-auto data-[active='true']:w-full data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500" modal data-modal-name="filter-modal" data-active="false">

    <div class="flex justify-between items-center">

        <div class="flex justify-center items-center gap-1 cursor-pointer mb-3" modal-closer data-modal-name="filter-modal">

            <i class="size-8 text-cynBlack">
                <?php Icon::print('Arrow,-Forward'); ?>
            </i>

            <span class="text-cynBlack text-base font-medium">
                <?php esc_html_e('بستن فیلتر ها', 'taghechian'); ?>
            </span>

        </div>

    </div>

    <?php wc_get_template('global/sidebar.php', array('sidebar_suffix' => 'mobile')); ?>

</section>