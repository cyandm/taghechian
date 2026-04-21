<?php

use Cyan\Theme\Helpers\Templates;

get_header();
?>

<?php
if (function_exists('is_cart') && (is_cart() || is_checkout() || is_wc_endpoint_url('order-received'))) {
    Templates::getPart('breadcrumb');
}
?>

<main class="container">
    <?php the_content(); ?>
</main>

<?php
get_footer();
?>